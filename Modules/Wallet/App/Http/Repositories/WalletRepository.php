<?php

namespace Modules\Wallet\App\Http\Repositories;
use Modules\Wallet\App\Models\WalletLog;
use Modules\Wallet\App\Models\WalletLogCategory;
use App\Models\User;
class WalletRepository
{
    /**
     * Create a new class instance.
     */
    public $model;
    public $walletLogCategory;

    public function __construct(WalletLog $walletModel , WalletLogCategory $walletLogCategory)
    {
        $this->model = $walletModel;
        $this->walletLogCategory = $walletLogCategory;
    }

    public function get($search = null){

        return $this->model->when($search != null ,function($querySearch)use($search){
            $querySearch->whereHas('student',function($queryStudent)use($search){
                $queryStudent->where('name','LIKE','%'.$search.'%');
            })->orWhere('balance','LIKE','%'.$search.'%');
        })->orderBy('id','desc')->get();
    }

    public function all()
    {
        return $this->model->all();
    }

    public function allCategory($search = null){

        return $this->walletLogCategory->when($search !=null ,function($querySearch) use ($search){
            $querySearch->wherehas('category',function($queryCategory) use ($search){

                $queryCategory->where('title','LIKE','%'.$search . '%');
            })->orWhere('balance','LIKE','%'.$search.'%');
        })->get();
    }

    public function find($id)
    {
        return WalletLog::findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    // public function update($id, array $data)
    // {
    //     $WalletLog = WalletLog::findOrFail($id);
    //     $WalletLog->update($data);
    //     return $WalletLog;
    // }
    public function delete($id)
    {
        $WalletLog = $this->model->findOrFail($id);
        $student = User::find($WalletLog->student_id);

        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        if ($WalletLog->type == 'fawery') {
            return response()->json(['message' => 'Cannot delete fawery transaction'], 400);
        }

        if ($student->status == 1) {
            if ($student->wallet >= $WalletLog->balance) {
                $student->decrement('wallet', $WalletLog->balance);
            } else {
                $student->update(['wallet' => 0]);
            }
        }

        $WalletLog->delete();

        return response()->json(['success' => true, 'message' => 'Wallet log deleted successfully']);
    }

    public function deleteCategory($id)
    {
        $WalletLogCategory = $this->walletLogCategory->findOrFail($id);
        $wallets  = $this->model->where('wallet_category_id',$id)->get();
        foreach($wallets as $wallet){
            $students = User::where('id',$wallet->student_id)->get();
            foreach($students as $student){
                if($student){
                    if ($student->wallet >= $wallet->balance) {
                        $student->decrement('wallet', $wallet->balance);
                    } else {
                        $student->update(['wallet' => 0]);
                    }
                        $wallet->delete();
                }
            }
        }
        $WalletLogCategory->delete();
        // dd( $student);

        return $WalletLogCategory;
    }
    public function changeStatus($id){

        // dd($payment_details);
        $wallet = $this->model->where('id',$id)->first();

        if ($wallet->type == 'fawery') {
            return response()->json(['message' => 'لا يمكن تعديل حاله فوري الان'], 400);
        }

        if($wallet){
            if($wallet->status == 0){
                $wallet->status ='1';
                $wallet->save();
                $wallet->student->increment('wallet',$wallet->balance);
                return true;

            }else{
                $wallet->status ='0';
                $wallet->save();
                  if( $wallet->student->wallet >= $wallet->balance){

                        $wallet->student->decrement('wallet',$wallet->balance);
                    }else{

                     $wallet->student->update(['wallet' => 0]);

                    }
                    return true;

                }
            }
    }
}
