<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\CouponLog;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CouponsExport; 
class CouponController extends Controller
{
    public function index(Request $request)
    {
        $query = Coupon::where('added_by', Auth::id());
        $type = 'discount';
        if(request()->filled('type')){
            $type = $request->type;
        }
        $query->where('type',$type);
        if (request()->has('search') && request()->query('search') != '') {
            $search = request()->query('search');
            if ($search) {
                $query = $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                      ->orWhere('title', 'like', "%{$search}%");
                });
            }
        }
        $page_data['coupons'] = $query->orderBy('id','DESC')->paginate(10)->appends(request()->query());
        return view('admin.coupon.index_'.$type, $page_data);
    }

    public function create()
    {
        $students = User::where('role','student')->get();
        $courses = \App\Models\Course::all();
        return view('admin.coupon.create', compact('students', 'courses'));
    }

    public function users_coupon($id){
        $log = CouponLog::where('coupon_id',$id)->with('coupons')->get();

        // foreach ($coupon_log as $coupon){
        //     $coupon = $coupon->coupons->code;
        //     $payment = User::whereHas('payments',function($q) use ($coupon){
        //         $q->where('coupon',$coupon);
        //     })->with('payments')->get();
        // }
        // return $payment->payments; 

        // $coupon_logs = CouponLog::where('coupon_id', $id)->with('coupons')->get();

        $query = User::query();
        if(request()->filled('search')){
            $search = request()->query('search');
            $query->where(function($q) use($search){
                $q->where('name','like','%'.$search.'%')
                ->orWhere('email','like','%'.$search.'%')
                ->orWhere('phone','like','%'.$search.'%');
            });
        }
    
    
        $users = $query->whereHas('couponLogs', function ($q) use ($id) {
            $q->where('coupon_id', $id);
        })->with(['payments', 'couponLogs'])->get();
    
        // return $users; 
        
        return view('admin.coupon.users_coupon',compact('log','users','id'));
    }
    public function store(Request $request)
    {
        $rules = [
            'type'         => 'required|in:recharge,discount,payment',
            'start_date'   => 'nullable|date|after_or_equal:today',
            'expiry'       => 'nullable|date|after_or_equal:start_date',
        ];
        
        $messages = [
            'expiry.after_or_equal' => 'تاريخ الانتهاء يجب أن يكون بعد أو مساوي لتاريخ البداية.',
            'start_date.after_or_equal' => 'تاريخ البداية لا يمكن أن يكون في الماضي.',
            'type.in' => 'نوع الكوبون يجب أن يكون recharge أو discount أو payment.',
        ];
        switch ($request->type) {
            case 'discount':
                $rules = array_merge($rules, [
                    'code'             => 'required|string|unique:coupons,code',
                    'title'            => 'required|string|max:255',
                    'discount'         => 'required|numeric|between:0,100',
                    'discount_type'    => 'required|in:first_purchase,all_purchases',
                    'minimum_amount'   => 'nullable|numeric|min:0',
                    'maximum_discount' => 'nullable|numeric|min:0',
                    'user_id'          => 'nullable|array|min:1',
                    'limit'            => 'nullable|integer|min:1',
                    'course_id'        => 'nullable|exists:courses,id',
                ]);
                break;
        
            case 'recharge':
                $rules = array_merge($rules, [
                    'value'         => 'required|numeric|min:1',
                    'coupon_count'  => 'required|integer|min:1|max:1000',
                ]);
                break;
        
            case 'payment':
                $rules = array_merge($rules, [
                    'value'              => 'required|numeric|min:1',
                    'user_id'            => 'required|array|min:1',
                    'limit'              => 'nullable|integer|min:1',
                    'balance_handling'   => 'required|array',
                    'balance_handling.*' => 'in:reuse,reuse_others,wallet',
                    'course_id'          => 'nullable|exists:courses,id',
                ]);
                break;
        }

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $coupon['code']       = $request->code;
        $coupon['status']     = 1;
        $coupon['added_by']   = Auth::id();
        $coupon['title']      = $request->title;
        $coupon['type']       = $request->type;
        $coupon['discount']   = $request->discount ?? 0;
        $coupon['value']      = $request->value ?? 0;
        $coupon['limit']      = $request->limit ?? null;
        $coupon['user_id']    = json_encode(array_map('intval', $request->user_id));
        $coupon['start_date'] = $request->start_date;
        $coupon['expiry']     = $request->expiry;
        $insertedCoupons      = [];
        // إضافة الحقول الجديدة
        $coupon['course_id'] = $request->course_id ?? null;
        // معالجة خيارات التعامل مع الرصيد المتبقي
        $coupon['balance_handling'] = json_encode($request->balance_handling);
        $coupon['minimum_amount'] = $request->minimum_amount;
        $coupon['maximum_discount'] = $request->maximum_discount;
        $coupon['discount_type'] = $request->discount_type ?? null;
        $fileName = 'coupons.xlsx';
        // معالجة خاصة لكوبونات الشحن
        if($request->type == 'recharge'){
            $couponCount                = $request->coupon_count ?? 1;
            $coupon['limit']            = 1;
            $coupon['user_id']          = json_encode([0]);
            $coupon['discount_type']    = null;
            $coupon['balance_handling'] = null;

            for ($i = 0; $i < $couponCount; $i++) {
                do {
                    $coupon['code'] = rand(1, 10000000);
                } while (Coupon::where('code', $coupon['code'])->exists());
            
                $newCoupon = Coupon::create($coupon);
                $insertedCoupons[] = $newCoupon;
            }
            $fileName = 'recharge_coupons_'.$coupon['value'].'.xlsx';
        }elseif($request->type == 'payment'){
            $couponCount                = $request->coupon_count ?? 1;
            $coupon['limit']            = 1;
            $coupon['user_id']          = json_encode([0]);
            $coupon['discount_type']    = null;

            for ($i = 0; $i < $couponCount; $i++) {
                do {
                    $coupon['code'] = rand(1, 10000000);
                } while (Coupon::where('code', $coupon['code'])->exists());
            
                $newCoupon = Coupon::create($coupon);
                $insertedCoupons[] = $newCoupon;
            }
            $fileName = 'recharge_coupons_'.$coupon['value'].'.xlsx';
        } else {
            // insert data
            $coupon['balance_handling'] = null;
            $coupon['value'] = $coupon['discount'];
            unset($coupon['discount']);
            Coupon::insert($coupon);
        }
        if ($request->has('download_excel')) {
            return Excel::download(new CouponsExport($insertedCoupons), $fileName);
        }
        
        // إذا تم اختيار الطباعة، نوجه المستخدم لصفحة الطباعة
        if ($request->has('printing') && !empty($insertedCoupons)) {
            $couponIds = array_column($insertedCoupons, 'id');
            return redirect()->route('admin.coupon.print', ['ids' => implode(',', $couponIds)]);
        }
        
        Session::flash('success', get_phrase('Coupon has been created successfully.'));
        return redirect()->route('admin.coupons');
    }

    public function delete($id)
    {
        // check user data exists or not
        $query = Coupon::where('id', $id)->where('added_by', Auth::id())->first();
        if ($query->doesntExist()) {
            Session::flash('error', get_phrase('Data not found.'));
            return redirect()->back();
        }
        $query->coupons_log()->delete();

        // delete data
        $query->delete();
        Session::flash('success', get_phrase('Coupon has ben deleted successfully.'));
        return redirect()->back();
    }

    public function edit($id)
    {
        // check user data exists or not
        $query = Coupon::where('id', $id)->where('added_by', Auth::id());
        if ($query->doesntExist()) {
            Session::flash('error', get_phrase('Data not found.'));
            return redirect()->back();
        }
        $page_data['students'] = User::where('role','student')->get();
        $page_data['courses'] = \App\Models\Course::all();
        $page_data['coupon_details'] = $query->first();
        return view('admin.coupon.edit', $page_data);
    }

    public function update(Request $request, $id)
    {
        // check user data exists or not
        $query = Coupon::where('id', $id)->where('added_by', Auth::id());
        if ($query->doesntExist()) {
            Session::flash('error', get_phrase('Data not found.'));
            return redirect()->back();
        }

        $rules = [
            'code'             => "required|string|unique:coupons,code,$id",
            'title'            => 'required|string',
            'start_date'       => 'nullable|date|after_or_equal:today',
            'value'            => 'required|numeric|min:1',
            'maximum_discount' => 'nullable|numeric|min:1',
            'minimum_amount'   => 'nullable|numeric|min:1',
            'discount_type'    => 'required|in:first_purchase,all_purchases',
            'user_id'          => 'required|array',
            'limit'            => 'nullable|numeric|between:1,1000000000',
            'expiry'           => 'nullable|date|after_or_equal:today',
        ];

        $messages = [
            'expiry.after_or_equal' => 'Expiry date must be a future date.',
            'status.in'             => 'Status must be either 0 or 1.',
            'type.in'               => 'Type must be recharge, discount, or payment.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $coupon['code']     = $request->code;
        $coupon['discount_type'] = $request->discount_type;
        $coupon['value']    = $request->value ?? 0;
        $coupon['added_by']  = Auth::id();
        $coupon['title']     = $request->title;
        $coupon['limit']     = $request->limit;
        $coupon['user_id']   = json_encode($request->user_id);
        $coupon['start_date'] = $request->start_date;
        $coupon['expiry']   = $request->expiry;
        
        // إضافة الحقول الجديدة
        $coupon['course_id'] = $request->course_id;
        // معالجة خيارات التعامل مع الرصيد المتبقي
        $coupon['minimum_amount'] = $request->minimum_amount;
        $coupon['maximum_discount'] = $request->maximum_discount;
        
        // insert data
        Coupon::where('id', $id)->update($coupon);

        Session::flash('success', get_phrase('Coupon has been updated successfully.'));
        return redirect()->back();
    }

    public function status($id)
    {
        // check user data exists or not
        $query = Coupon::where('id', $id)->where('added_by', Auth::id());
        if ($query->doesntExist()) {
            Session::flash('error', get_phrase('Data not found.'));
            return redirect()->back();
        }

        $coupon = $query->first();
        $query->update(['status' => $coupon->status ? 0 : 1]);
        Session::flash('success', get_phrase('Status has been updated'));
        return redirect(route('admin.coupons'));
    }

    public function print(Request $request)
    {
        $ids = $request->get('ids');
        if (empty($ids)) {
            Session::flash('error', get_phrase('No coupons selected for printing.'));
            return redirect()->route('admin.coupons');
        }

        $couponIds = explode(',', $ids);
        $coupons = Coupon::whereIn('id', $couponIds)
            ->where('added_by', Auth::id())
            ->with('addedBy')
            ->get();

        if ($coupons->isEmpty()) {
            Session::flash('error', get_phrase('Coupons not found.'));
            return redirect()->route('admin.coupons');
        }

        return view('admin.coupon.print', compact('coupons'));
    }

    public function printAll($type)
    {
        $coupons = Coupon::where('type',$type)->orderBy('id','DESC')->get();
        return view('admin.coupon.print', compact('coupons'));
    }
}
