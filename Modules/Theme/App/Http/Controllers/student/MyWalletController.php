<?php

namespace Modules\Theme\App\Http\Controllers\student;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\CouponLog;
use App\Models\FileUploader;
use App\Models\payment_gateway\Wallet;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Modules\Theme\App\Http\Services\FawryPay;
use Modules\Theme\App\Http\Services\PaymobService;
use Modules\Wallet\App\Models\WalletLog;
use Illuminate\Support\Facades\Log;

class MyWalletController extends Controller
{
    public function index()
    {
        $userId = auth()->user()->id;

        $page_data['user_wallets'] = WalletLog::with('added')
            ->where('student_id', $userId)
            ->orderBy('id', 'DESC')
            ->paginate(15);

        $page_data['payment_gateways'] = DB::table('payment_gateways')
            ->where('status', 1)
            ->where('identifier', '!=', 'Wallet')
            ->get();

        $page_data['wallet_balance'] = auth()->user()->wallet ?? 0;
        $page_data['stats'] = [
            'credits' => WalletLog::where('student_id', $userId)
                ->where('status', true)
                ->where('type', '!=', 'decreased')
                ->sum('balance'),
            'debits' => WalletLog::where('student_id', $userId)
                ->where('status', true)
                ->where('type', 'decreased')
                ->sum('balance'),
            'pending' => WalletLog::where('student_id', $userId)
                ->where('status', false)
                ->count(),
        ];

        return view('theme::student.my_wallet.index', $page_data);
    }

    public function show_payment_gateway_by_ajax($identifier,$balance)
    {

        $page_data['payment_gateway'] = DB::table('payment_gateways')->where('identifier', $identifier)->first();
        if($page_data['payment_gateway']->identifier == "fawrypay"){
            $fawryPay = new FawryPay;
            $fawryPay->customer([
                'customerProfileId' => auth()->user()->id,
                'name'              => auth()->user()->name,
                'email'             => auth()->user()->email,
                'mobile'            => auth()->user()->phone
            ]);
            $payment_details[] = [
                'productSKU'    => rand(10,9999),
                'type'          => 'charge',
                'description'   => 'شحن رصيد للمحفظة',
                'price'         => $balance,
                'quantity'      => 1,
            ];
            $uuid = $this->saveCharge($identifier , $balance);
            $page_data['payment_details']['uuid'] = $uuid;
            Session::put('wallet_charge_details', $page_data);
            $pay_url = $fawryPay->generatePayURL($uuid,'wallet charging',route('theme.wallet.fawry.callback'),route('theme.wallet.fawry.callback'));
            return ['url'=>$pay_url,'uuid'=>$uuid];
        }
        if($page_data['payment_gateway']->identifier == "paymob"){
            $paymob = new PaymobService;
            $userData = [
                'user_id' => auth()->user()->id,
                'name'    => auth()->user()->name,
                'email'   => auth()->user()->email,
                'phone'   => auth()->user()->phone
            ];

            $uuid = $this->saveCharge($identifier , $balance);
            $page_data['payment_details']['uuid'] = $uuid;
            Session::put('wallet_charge_details', $page_data);
            $redirectUrl = $paymob->createWalletPayment($userData, $balance, $uuid);
            return ['url' => $redirectUrl, 'uuid' => $uuid];
        }
        $uuid = $this->saveCharge($identifier , $balance);
        $page_data['payment_details']['balance'] = $balance;
        $page_data['payment_details']['uuid'] = $uuid;
        Session::put('wallet_charge_details', $page_data);
        $page_data['payment_details']['success_url'] = route('theme.payment.wallet.success',$identifier);
        return view('payment.' . $identifier . '.wallet_charge', $page_data);
    }


    public function fawryHandleCallback(Request $request){
        Log::info('Fawry Callback:', $request->all());
        if (!$request->has(['paymentStatus', 'merchantRefNumber'])) {
            return response()->json(['message' => 'Missing required parameters'], 422);
        }
        $status = $request->paymentStatus;
        $referenceNumber = $request->merchantRefNumber;
        $log = WalletLog::where('uuid',$referenceNumber)->first();
        if (!$log) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $log->payment_id = $request->fawryRefNumber ?? null;
        if ($status === 'PAID') {
            $log->status = '1';
            $log->student->increment('wallet',$log->balance);
        } elseif ($status === 'FAILED') {
            $log->status = '0';
        }
        $log->save();
        return response()->json(['message' => 'Callback processed'], 200);
    }

    public function paymobHandleCallback(Request $request)
    {
        Log::info('Paymob Callback:', $request->all());

        $status = $request->input('success');
        $referenceNumber = $request->input('merchant_order_id');
        $transactionId = $request->input('id');

        $log = WalletLog::where('uuid',$referenceNumber)->first();

        if (!$log) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $log->payment_id = $transactionId;
        if ($status == true || $status == 1 || $request->input('txn_response_code') == 'APPROVED') {
            $log->status = '1';
            $log->student->increment('wallet',$log->balance);
        } else {
            $log->status = '0';
        }
        $log->save();
        return response()->json(['message' => 'Paymob callback processed'], 200);
    }

    public function payment_success($identifier, Request $request)
    {
        $payment_details = session('wallet_charge_details');
        $payment_gateway = DB::table('payment_gateways')->where('identifier', $identifier)->first();
        $model_name      = $payment_gateway->model_name;
        $model_full_path = str_replace(' ', '', 'App\Models\payment_gateway\ ' . $model_name);
        $status = $model_full_path::payment_status($identifier, $request->all());
        if ($status === true) {
            // dd($payment_details);
            if($this->paidCharge($payment_details,$request) == true){
                return to_route('theme.my.wallet')->with('success','Payment success');
            }else{
                return to_route('theme.my.wallet')->with('error','Payment failed');
            }
        } else {
            return to_route('theme.my.wallet')->with('error','Payment failed');
        }
    }



    public function paidCharge($payment_details,$request){
        // dd($payment_details);
        $log = WalletLog::where('uuid',$payment_details['payment_details']['uuid'])->first();
        if($log){
            if($log->status == false){
                $log->status = '1';
                $log->payment_id = $request->payment_id;
                $log->save();
                $log->student->increment('wallet',$log->balance);
            }
            return true;
        }
        return false;
    }


    public function saveCharge($identifier ,$balance , $status = '0'){
        $uuid = Str::uuid();
        WalletLog::create([
            'student_id' => auth()->user()->id,
            'added_by'   => auth()->user()->id,
            'uuid'       => $uuid,
            'status'     => $status,
            'type'       => $identifier,
            'balance'    => $balance
        ]);
        return $uuid;
    }

    public function verify_card(Request $request)
    {
        $card_code = $request->card_code;
        $coupon = Coupon::where('code',$card_code)->first();

        if($coupon->type != 'recharge'){
            return response()->json(['success' => false,'message' => 'Card is not a recharge card']);
        }

        if(!$coupon->isValid()){
            return response()->json(['success' => false,'message' => 'Card is not valid']);
        }
        if(!$coupon->canBeUsedForUser(auth()->user()->id)){
            return response()->json(['success' => false,'message' => 'Card is not for you']);
        }

        $this->saveCharge('card',$coupon->value, '1');
        CouponLog::create([
            'coupon_id' => $coupon->id,
            'user_id' => auth()->user()->id,
        ]);
        auth()->user()->increment('wallet',$coupon->value);
        return response()->json(['success' => true,'amount' => $coupon->value,'message' => 'Card verified successfully']);
    }


}
