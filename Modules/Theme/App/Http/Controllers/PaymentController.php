<?php

namespace Modules\Theme\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Anand\LaravelPaytmWallet\Facades\PaytmWallet;
use App\Models\Coupon;
use App\Models\CouponLog;
use App\Models\FileUploader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Payment_history;
use App\Models\Course;
use Carbon\Carbon;
use Modules\BookStore\App\Models\Book;
use Modules\Theme\App\Http\Services\FawryPay;
use Modules\Theme\App\Http\Services\PaymobService;
use Illuminate\Support\Facades\Log;
use Modules\Wallet\App\Models\WalletLog;

class PaymentController extends Controller
{
    public function index()
    {
        $payment_details = session('payment_details');
        // return $payment_details;
        if (!$payment_details || !is_array($payment_details) || count($payment_details) <= 0) {
            Session::flash('error', get_phrase('Payment not configured yet'));
            return redirect()->back();
        }
        if ($payment_details['payable_amount'] <= 0) {
            Session::flash('error', get_phrase("Payable amount cannot be less than 1"));
            return redirect()->to($payment_details['cancel_url']);
        }

        $page_data['payment_details']  = $payment_details;
        $page_data['payment_gateways'] = DB::table('payment_gateways')->where('status', 1)->get();
        return view('theme::payment.index', $page_data);
    }

    public function show_payment_gateway_by_ajax($identifier)
    {
        $page_data['payment_details'] = session('payment_details');
        // dd( $page_data['payment_details']);
        $page_data['payment_gateway'] = DB::table('payment_gateways')->where('identifier', $identifier)->first();

        if(isset($page_data['payment_details']['coupon']) && $page_data['payment_details']['coupon'] != ''){
            $coupon = Coupon::where('code',$page_data['payment_details']['coupon'])->where('type','discount')->first();
            if (!$coupon) {
                return ['status' => false, 'message' => get_phrase('الكوبون غير صالح.')];
            }          
            
            [$isValid, $message] = $coupon->validateForDiscount(auth()->user()->id, $page_data['payment_details']['total_amount']);

            if (!$isValid) {
                return ['status' => false, 'message' => $message];
            }
            
            CouponLog::create([
                'coupon_id'       => $coupon->id,
                'user_id'         => auth()->user()->id,
                'used_amount'     => $page_data['payment_details']['custom_field']['coupon_discount'],
                'remaining_after' => 0,
            ]);
        }
        if($page_data['payment_gateway']->identifier == "fawrypay"){
            $fawryPay = new FawryPay;
            $fawryPay->customer([
                'customerProfileId' => $page_data['payment_details']['custom_field']['user_id'],
                'name'              => $page_data['payment_details']['custom_field']['user_name'],
                'email'             => $page_data['payment_details']['custom_field']['user_email'],
                'mobile'            => $page_data['payment_details']['custom_field']['user_phone']
            ]);
            $payment_details = [];
            foreach($page_data['payment_details']['items'] as $key=>$item){
                $payment_details[$key] = [
                    'productSKU'    => $item['id'],
                    'type'          => $item['type'],
                    'description'   => $item['title'],
                    'price'         => $item['discount_price'] > 0 ? $item['discount_price'] : $item['price'],
                    'quantity'      => $item['qty'],
                ];
                $fawryPay->addItem($payment_details[$key]);
            }
            $uuid = $this->saveInvoice($identifier);
            $pay_url = $fawryPay->generatePayURL($uuid,'Order Invoice',route('theme.fawry.callback'),route('theme.fawry.callback'));
            return ['url'=>$pay_url,'uuid'=>$uuid];
        }
        if($page_data['payment_gateway']->identifier == "paymob"){
            $paymob = new PaymobService;

            $userData = [
                'user_id'    => $page_data['payment_details']['custom_field']['user_id'],
                'name'       => $page_data['payment_details']['custom_field']['user_name'],
                'email'      => $page_data['payment_details']['custom_field']['user_email'],
                'phone'      => $page_data['payment_details']['custom_field']['user_phone'],
            ];

            $totalPrice =  $page_data['payment_details']['payable_amount'] ?? 0;
            $uuid = $this->saveInvoice($identifier);
            $redirectUrl = $paymob->createWalletPayment($userData, $totalPrice, $uuid);
            return ['url' => $redirectUrl, 'uuid' => $uuid];
        }
        if($identifier == "Wallet"){
            $identifier = "wallet";
        }
        return view('payment.' . $identifier . '.index', $page_data);
    }

    public function saveInvoice($identifier){
        $uuid = Str::uuid();
        $payment_details = session('payment_details');
        $revenue = [
            'admin'      => 0,
            'instructor' => 0
        ];

        $invoice = new Payment_history();
        $invoice->amount         = $payment_details['payable_amount'];
        $invoice->user_id        = auth()->user()->id;
        $invoice->payment_type   = $identifier;
        $invoice->tax            = $payment_details['tax'];
        $invoice->status         = 'un-paid';
        $invoice->uuid           = $uuid;
        $invoice->coupon         = $payment_details['coupon'];
        $invoice->invoice        = $payment_details['invoice'];
        $invoice->save();

        for ($i = 0; $i < count($payment_details['items']); $i++) {

            $price           = $payment_details['items'][$i]['price'];
            $course_discount = $payment_details['items'][$i]['discount_price'];
            $realPrice       = $course_discount > 0 ? $course_discount : $price;
            if($payment_details['items'][$i]['type'] == 'course'){

                if (get_course_creator_id($payment_details['items'][$i]['id'])->role == 'admin') {
                    $revenue['admin'] += $realPrice;
                } else {
                    $revenueInstructor     = $realPrice * (get_settings('instructor_revenue') / 100);
                    $revenue['instructor'] += $revenueInstructor;
                    $revenue['admin']      += $realPrice - $revenueInstructor;
                }

                $course = Course::where('id',$payment_details['items'][$i]['id'])->first();
                if($course){
                    $course->invoice()->attach([
                        $invoice->id => [
                            'amount'  => $realPrice ,
                            'qty'     => $payment_details['items'][$i]['qty'] ?? 1
                        ],
                    ]);

                    $enroll['course_id']       = $payment_details['items'][$i]['id'];
                    $enroll['user_id']         = $payment_details['custom_field']['gifted_user_id'] ? $payment_details['custom_field']['gifted_user_id'] : auth()->user()->id;
                    $enroll['enrollment_type'] = "paid";
                    $enroll['entry_date']      = time();

                    $course_details = get_course_info($payment_details['items'][$i]['id']);

                    if ($course_details->expiry_period > 0) {
                        $days = $course_details->expiry_period * 30;
                        $enroll['expiry_date'] = strtotime("+" . $days . " days");
                    } else {
                        $enroll['expiry_date'] = null;
                    }
                }
            }elseif($payment_details['items'][$i]['type'] == 'book'){
                $book = Book::where('id',$payment_details['items'][$i]['id'])->first();
                if($book){
                    $book->invoice()->attach([
                        $invoice->id => [
                            'amount'  => $realPrice,
                            'qty'     => $payment_details['items'][$i]['qty'] ?? 1
                        ],
                    ]);
                }
            }
        }

        $invoice->admin_revenue      =  $revenue['admin'];
        $invoice->instructor_revenue =  $revenue['instructor'];
        $invoice->save();
        return $uuid;
    }

    public function payment_successFree($id, Request $request)
    {

        if(auth()->user()->role == 'student'){


        if (Session::has('keys')) {
            $transaction_keys          = session('keys');
            $transaction_keys          = json_encode($transaction_keys);
            $payment['transaction_id'] = $transaction_keys;
            $remove_session_item[]     = 'keys';
        }
        if (Session::has('session_id')) {
            $transaction_keys      = session('session_id');
            $payment['session_id'] = $transaction_keys;
            $remove_session_item[] = 'session_id';
        }

        // generate invoice for payment
        $payment['invoice'] = Str::random(20);


        $invoice = new Payment_history();
        $invoice->amount         = 0;
        $invoice->user_id        = auth()->user()->id;
        $invoice->payment_type   = 'free';
        $invoice->tax            = 0;
        $invoice->coupon         = 0;
        $invoice->invoice        = $payment['invoice'];
        $invoice->paid           = 1;
        $invoice->transaction_id = $payment['transaction_id'] ?? null;
        $invoice->session_id     = $payment['session_id'] ?? null;
        $invoice->save();






        $course = Course::where('id',$id)->first();
        // dd($course->is_paid);
        if($course){
                if ($course->is_paid == 0) {
                    $course->invoice()->attach([
                        $invoice->id => [
                            'amount' => 0,
                        ],
                    ]);
                }

            $enroll['course_id']       = $course->id;
            $enroll['user_id']         =  auth()->user()->id;
            $enroll['enrollment_type'] = "paid";
            $enroll['entry_date']      = time();


            if ($course->expiry_period > 0) {
                $days = $course->expiry_period * 30;
                $enroll['expiry_date'] = strtotime("+" . $days . " days");
            } else {
                $enroll['expiry_date'] = null;
            }

            // insert a new enrollment
            DB::table('enrollments')->insert($enroll);
        }



        $invoice->admin_revenue      =  0;
        $invoice->instructor_revenue =  0;
        $invoice->save();
        // if payment and enroll has been done then remove items from cart




        $remove_session_item[] = 'payment_details';
        Session::forget($remove_session_item);
        Session::flash('success', 'Course enrolled successfully.');
        return redirect()->route('theme.my.courses');


        }else{
        Session::flash('error', 'لا يمكنك الاضافه.');
        return redirect()->back();
        }
    }
    public function payment_success($identifier, Request $request)
    {

        $payment_details = session('payment_details');
        $payment_gateway = DB::table('payment_gateways')->where('identifier', $identifier)->first();
        $model_name      = $payment_gateway->model_name;
        $model_full_path = str_replace(' ', '', 'App\Models\payment_gateway\ ' . $model_name);
        $status = $model_full_path::payment_status($identifier, $request->all());


        if ($status === true) {

            $success_model    = $payment_details['success_method']['model_name'];
            $success_function = $payment_details['success_method']['function_name'];
            $model_full_path = str_replace(' ', '', 'App\Models\ ' . $success_model);
            return $model_full_path::$success_function($identifier);
        } else {
            Session::flash('error', $status ?? get_phrase('Payment failed! Please try again.'));
            // return redirect()->to($payment_details['cancel_url']);
            return redirect()->back();
        }
    }


    public function fawryHandleCallback(Request $request){
        Log::info('Fawry Callback:', $request->all());
        if (!$request->has(['paymentStatus', 'merchantRefNumber'])) {
            return response()->json(['message' => 'Missing required parameters'], 422);
        }
        $status = $request->paymentStatus;
        $referenceNumber = $request->merchantRefNumber;
        $order = Payment_history::where('uuid',$referenceNumber)->first();
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }
        $order->transaction_id = $request->fawryRefNumber ?? null;
        if ($status === 'PAID') {
            $order->status = 'paid';
        } elseif ($status === 'FAILED') {
            $order->status = 'failed';
        }
        $order->save();

        $enroll = [];
        foreach($order->items as $key=>$item){
            $type = class_basename($item->productable_type);
            if($type == "Course"){
                $enroll['course_id']       = $item->productable_id;
                $enroll['user_id']         = $order->user_id;
                $enroll['enrollment_type'] = "paid";
                $enroll['entry_date']      = time();

                $course_details = get_course_info($enroll['course_id']);

                if ($course_details->expiry_period > 0) {
                    $days = $course_details->expiry_period * 30;
                    $enroll['expiry_date'] = Carbon::now()->addDays($days);
                } else {
                    $enroll['expiry_date'] = null;
                }

                $exists = DB::table('enrollments')->where('course_id', $enroll['course_id'])->where('user_id', $enroll['user_id'])->exists();
                if (!$exists) {
                    DB::table('enrollments')->insert($enroll);
                }
            }
        }
        return response()->json(['message' => 'Callback processed'], 200);
    }

    public function paymobHandleCallback(Request $request)
    {
        Log::info('Paymob Callback:', $request->all());

        $status = $request->input('success');
        $referenceNumber = $request->input('merchant_order_id');
        $transactionId = $request->input('id');

        $order = Payment_history::where('uuid', $referenceNumber)->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }
        $order->transaction_id = $transactionId;
        if ($status == true || $status == 1 || $request->input('txn_response_code') == 'APPROVED') {
            $order->status = 'paid';
        } else {
            $order->status = 'failed';
        }
        $order->save();
        $enroll = [];
        foreach ($order->items as $key => $item) {
            $type = class_basename($item->productable_type);
            if ($type == "Course") {
                $enroll['course_id']       = $item->productable_id;
                $enroll['user_id']         = $order->user_id;
                $enroll['enrollment_type'] = "paid";
                $enroll['entry_date']      = time();

                $course_details = get_course_info($enroll['course_id']);

                if ($course_details->expiry_period > 0) {
                    $days = $course_details->expiry_period * 30;
                    $enroll['expiry_date'] = now()->addDays($days);
                } else {
                    $enroll['expiry_date'] = null;
                }

                $exists = DB::table('enrollments')
                    ->where('course_id', $enroll['course_id'])
                    ->where('user_id', $enroll['user_id'])
                    ->exists();

                if (!$exists) {
                    DB::table('enrollments')->insert($enroll);
                }
            }
        }

        return response()->json(['message' => 'Paymob callback processed'], 200);
    }




    public function payment_create($identifier)
    {
        $payment_details      = session('payment_details');
        $payment_gateway      = DB::table('payment_gateways')->where('identifier', $identifier)->first();
        $model_name           = $payment_gateway->model_name;
        $model_full_path      = str_replace(' ', '', 'App\Models\payment_gateway\ ' . $model_name);
        $created_payment_link = $model_full_path::payment_create($identifier);

        return redirect()->to($created_payment_link);
    }

    public function payment_razorpay($identifier)
    {
        $payment_details = session('payment_details');
        $payment_gateway = DB::table('payment_gateways')->where('identifier', $identifier)->first();
        $model_name      = $payment_gateway->model_name;
        $model_full_path = str_replace(' ', '', 'App\Models\payment_gateway\ ' . $model_name);
        $data            = $model_full_path::payment_create($identifier);

        return view('payment.razorpay.payment', compact('data'));
    }

    public function verify_card(Request $request)
    {
        $payment_details = session('payment_details');

        if(isset($payment_details['coupon']) && $payment_details['coupon'] != ''){
            $coupon = Coupon::where('code',$payment_details['coupon'])->where('type','discount')->first();
            if (!$coupon) {
                return ['status' => false, 'message' => get_phrase('الكوبون غير صالح.')];
            }          
            
            [$isValid, $message] = $coupon->validateForDiscount(auth()->user()->id, $payment_details['total_amount']);

            if (!$isValid) {
                return ['status' => false, 'message' => $message];
            }
            
            CouponLog::create([
                'coupon_id'       => $coupon->id,
                'user_id'         => auth()->user()->id,
                'used_amount'     => $payment_details['custom_field']['coupon_discount'],
                'remaining_after' => 0,
            ]);
        }

        $card_code = $request->card_code;
        $coupon = Coupon::where('code',$card_code)->first();

        if(!$coupon || $coupon->type != 'payment'){
            return response()->json(['success' => false,'message' => 'الكوبون غير صالح.']);
        }

        if(!$coupon->isValid()){
            return response()->json(['success' => false,'message' => 'الكوبون غير صالح.']);
        }
        $coupon_value = $coupon->value;
        if($coupon->is_partially_used){
            $coupon_value = $coupon->remaining_value;
        }

        if($payment_details['payable_amount'] > $coupon_value){
            return response()->json(['success' => false,'message' => 'قيمة الكوبون غير كافية.']);
        }

        $remainingBalance = $coupon->value - $payment_details['payable_amount'];
        if($coupon->is_partially_used){
            $remainingBalance = $coupon->remaining_value - $payment_details['payable_amount'];
        }
        if($remainingBalance > 0){
            $balance_handling = json_decode($coupon->balance_handling,true);
            if(in_array('wallet',$balance_handling)){
                auth()->user()->increment('wallet',$remainingBalance);
                
                WalletLog::create([
                    'student_id' => auth()->user()->id,
                    'added_by'   => auth()->user()->id,
                    'uuid'       => Str::uuid(),
                    'status'     => '1',
                    'type'       => 'card',
                    'balance'     => $remainingBalance,
                ]);

                $coupon->update([
                    'remaining_value'   => 0,
                    'is_partially_used' => true,
                    'used_by'           => auth()->id(),
                ]);
            } elseif (in_array('reuse', $balance_handling)) {
                $coupon->update([
                    'remaining_value'   => $remainingBalance,
                    'is_partially_used' => true,
                    'used_by'           => auth()->id(),
                ]);
            } elseif (in_array('reuse_others', $balance_handling)) {
                $coupon->update([
                    'remaining_value'   => $remainingBalance,
                    'is_partially_used' => true,
                    'used_by'           => null,
                ]);
            }
            CouponLog::create([
                'coupon_id'       => $coupon->id,
                'user_id'         => auth()->id(),
                'used_amount'     => $payment_details['payable_amount'],
                'remaining_after' => $remainingBalance,
            ]);
        }
        $success_model    = $payment_details['success_method']['model_name'];
        $success_function = $payment_details['success_method']['function_name'];
        $model_full_path  = str_replace(' ', '', 'App\Models\ ' . $success_model);
        $model_full_path::$success_function('card');
        return response()->json([
            'success'  => true,
            'amount'   => $payment_details['payable_amount'],
            'type'     => $coupon->type,
            'message'  => 'تم التحقق بنجاح.'
        ]);
    }
































    public function make_paytm_order(Request $request)
    {
        return view('payment.paytm.paytm_merchant_checkout');
    }

    public function paytm_paymentCallback()
    {
        $transaction = PaytmWallet::with('receive');
        $response    = $transaction->response();
        $order_id    = $transaction->getOrderId(); // return a order id
        $transaction->getTransactionId(); // return a transaction id

        // update the db data as per result from api call
        if ($transaction->isSuccessful()) {
            Paytm::where('order_id', $order_id)->update(['status' => 1, 'transaction_id' => $transaction->getTransactionId()]);
            return redirect(route('initiate.payment'))->with('message', "Your payment is successfull.");
        } else if ($transaction->isFailed()) {
            Paytm::where('order_id', $order_id)->update(['status' => 0, 'transaction_id' => $transaction->getTransactionId()]);
            return redirect(route('initiate.payment'))->with('message', "Your payment is failed.");
        } else if ($transaction->isOpen()) {
            Paytm::where('order_id', $order_id)->update(['status' => 2, 'transaction_id' => $transaction->getTransactionId()]);
            return redirect(route('initiate.payment'))->with('message', "Your payment is processing.");
        }
        $transaction->getResponseMessage(); //Get Response Message If Available

    }

    public function webRedirectToPayFee(Request $request)
    {
        // Check if the 'auth' query parameter is present
        if (!$request->has('auth')) {
            return redirect()->route('login')->withErrors([
                'email' => 'Authentication token is missing.',
            ]);
        }

        // Remove the 'Basic ' prefix
        // $base64Credentials = $request->query('auth');
        // Remove the 'Basic ' prefix
        $base64Credentials = substr($request->query('auth'), 6);

        // Decode the base64-encoded string
        $credentials = base64_decode($base64Credentials);

        // Split the decoded string into email, password, and timestamp
        list($email, $password, $timestamp) = explode(':', $credentials);

        // Get the current timestamp
        $timestamp1 = strtotime(date('Y-m-d'));

        // Calculate the difference
        $difference = $timestamp1 - $timestamp;

        if ($difference < 86400) {
            if (auth()->attempt(['email' => $email, 'password' => $password])) {
                // Authentication passed...
                return redirect(route('cart'));
            }

            return redirect()->route('login')->withErrors([
                'email' => 'Invalid email or password',
            ]);
        } else {
            return redirect()->route('login')->withErrors([
                'email' => 'Token expired!',
            ]);
        }
    }
}
