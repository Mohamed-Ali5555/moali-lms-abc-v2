<?php

namespace Modules\Wallet\App\Http\Services;
use Modules\Wallet\App\Http\Repositories\WalletRepository;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
class WalletService
{
    /**
     * Create a new class instance.
     */
    protected $walletRepository;

    public function __construct(WalletRepository $walletRepository)
    {
        return $this->walletRepository = $walletRepository;
    }

    public function getWallets($search)
    {
        return $this->walletRepository->get($search);
    }

    public function getAllWallets()
    {
        return $this->walletRepository->all();
    }

    public function getAllWalletsCategory($search)
    {
        return $this->walletRepository->allCategory($search);
    }


    public function createWalletCategory($data)
    {
        try{
        $data['added_by'] = Auth::id();

        $students = User::where('category',$data['category_id'])->get();
       if($students->isEmpty()){
            return ['status' => false, 'message' => 'No students found with the given IDs.'];
       }
       $wallet_category = new $this->walletRepository->walletLogCategory;
       $wallet_category->balance                  = $data['balance'];
       $wallet_category->type                     = $data['type'];
       $wallet_category->category_id              = $data['category_id'];
       $wallet_category->note                     = $data['note'];

       $wallet_category->added_by                 = Auth::id();
       $wallet_category->status                   = '1';
       $wallet_category->save();

       $walletCharges = [];
        foreach($students as $student){

             $walletCharge                           = new $this->walletRepository->model;
             $walletCharge->student_id               = $student->id;
             $walletCharge->balance                  = $data['balance'];
             $walletCharge->type                     = $data['type'];
             $walletCharge->wallet_category_id       = $wallet_category->id;

             $walletCharge->uuid                     = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 11);
            //  $walletCharge->note                  = $request['note'];
             $walletCharge->added_by                 = Auth::id();
             $walletCharge->status                   = '1';
             $walletCharge->save();
             $walletCharges[] = $walletCharge;
             $student->increment('wallet', $data['balance']);
        }

            return ['status' => true, 'message' => 'Wallet category charges added successfully'];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => 'An error occurred: ' . $e->getMessage()];
        }
    }
    public function createWallet($data)
    {
        try {
        $data['added_by'] = Auth::id();
        $data['uuid'] = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 11);

        $studentIds = array_map('intval', $data['student_id']);
        $students = User::whereIn('id', $studentIds)->get();
        if ($students->isEmpty()) {
            return response()->json(['error' => 'No students found with the given IDs.'], 400);
        }
        foreach($students as $student){

             $walletCharge             = new $this->walletRepository->model;
             $walletCharge->student_id = $student->id;
             $walletCharge->balance    = $data['balance'];
             $walletCharge->type       = $data['type'];
             $walletCharge->uuid       = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 11);
             $walletCharge->note       = $data['note'];
             $walletCharge->added_by   = Auth::id();
             $walletCharge->status     = '1';
             $walletCharge->save();


             $student->increment('wallet', $data['balance']);
        }
        //   return $walletCharge;
         return response()->json(['success' => 'Wallet charges added successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function decreasWallet($data)
    {
        // return $data;
        try {
        $data['added_by'] = Auth::id();
        $data['uuid'] = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 11);

        $studentIds = array_map('intval', $data['student_id']);
        $students = User::whereIn('id', $studentIds)->get();
        if ($students->isEmpty()) {
            return response()->json(['error' => 'No students found with the given IDs.'], 400);
        }
      $students_equal_zero = [];

        foreach($students as $student){
            if ($student->wallet == 0) {
               return redirect()->back()->with('error','محفظه هذا الطالب لا تحتوي علي رصيد.');
            }
            $real_balance=0;

             $walletCharge             = new $this->walletRepository->model;
             $walletCharge->student_id = $student->id;
             $walletCharge->balance    = $data['balance'];
             $walletCharge->type       = 'decreased';
             $walletCharge->uuid       = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 11);
             $walletCharge->note       = $data['note'];
             $walletCharge->added_by   = Auth::id();
             $walletCharge->status     = '1';



           if($student->wallet <= $data['balance']){
                $real_balance             = $student->wallet;
                $walletCharge->balance    =  $student->wallet;
                $student->wallet = 0;
                $student->save();
            }else{
                $student->decrement('wallet', $data['balance']);
                $real_balance =$data['balance'];
            }



                if($real_balance != 0){
                    $walletCharge->save();
                }else{
                    $students_equal_zero[] = $student->name;
                }

        }

        if (count($students_equal_zero) > 0) {
            $message = 'تمت العمليه ولكن هؤلاء الطلاب لم يتم التنفيذ لهم ';
            foreach ($students_equal_zero as $name) {
                $message .= '<br>' . $name;
            }
            // dd(($message));
            return redirect()->route('admin.wallet.index')->with('error',$message);

            // return response()->json(['error' => $message],400);
        }

        //   return $walletCharge;
         return redirect()->route('admin.wallet.index')->with('success', 'balance decreasd successfully.');
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function deleteWallet($id){
        try{
            $deleted = $this->walletRepository->delete($id);
            if($deleted){
                return response()->json(['success' => 'Wallet deleted successfully.'], 200);

            }else{
                 return response()->json(['error' => 'Failed to delete wallet.'], 500);

            }
         } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }

    }

    public function deleteWalletCategory($id){
        try{
            $deleted = $this->walletRepository->deleteCategory($id);

            if($deleted){
                return response()->json(['success' => 'category Wallet deleted successfully.'], 200);

            }else{
                 return response()->json(['error' => 'Failed to delete category wallet.'], 500);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }



        public function changeStatus($id){
        try{
            $row = $this->walletRepository->changeStatus($id);
            if($row){
                return response()->json(['success' => 'Wallet status changed successfully.'], 200);

            }else{
                 return response()->json(['error' => 'Failed to change status wallet.'], 500);

            }
         } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }

    }

}
