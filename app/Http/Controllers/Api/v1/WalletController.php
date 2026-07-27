<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\CouponLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Theme\App\Http\Services\FawryPay;
use Modules\Theme\App\Http\Services\PaymobService;
use Modules\Wallet\App\Models\WalletLog;

class WalletController extends Controller
{
   

    public function show_payment_gateway_by_ajax(Request $request, $identifier, $balance)
    {
        $payment_gateway = DB::table('payment_gateways')->where('identifier', $identifier)->first();

        if (!$payment_gateway) {
            return response()->json(['message' => 'Payment gateway not found'], 404);
        }

        $user = $request->user();

        if ($payment_gateway->identifier == 'fawrypay') {
            $fawryPay = new FawryPay;
            $fawryPay->customer([
                'customerProfileId' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'mobile' => $user->phone,
            ]);

            $uuid = $this->saveCharge($identifier, $balance);
            $pay_url = $fawryPay->generatePayURL(
                $uuid,
                'wallet charging',
                route('theme.wallet.fawry.callback'),
                route('theme.wallet.fawry.callback')
            );

            return response()->json(['url' => $pay_url, 'uuid' => $uuid], 200);
        }

        if ($payment_gateway->identifier == 'paymob') {
            $paymob = new PaymobService;
            $userData = [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ];

            $uuid = $this->saveCharge($identifier, $balance);
            $redirectUrl = $paymob->createWalletPayment($userData, $balance, $uuid);

            return response()->json(['url' => $redirectUrl, 'uuid' => $uuid], 200);
        }

        $uuid = $this->saveCharge($identifier, $balance);

        return response()->json([
            'uuid' => $uuid,
            'balance' => $balance,
            'success_url' => url('api/v1/wallet/success/' . $identifier . '?uuid=' . $uuid),
        ], 200);
    }

    public function payment_success($identifier, Request $request)
    {
        if (!$request->uuid) {
            return response()->json(['message' => 'uuid is required'], 422);
        }

        $payment_gateway = DB::table('payment_gateways')->where('identifier', $identifier)->first();

        if (!$payment_gateway) {
            return response()->json(['message' => 'Payment gateway not found'], 404);
        }

        $model_name = $payment_gateway->model_name;
        $model_full_path = str_replace(' ', '', 'App\Models\payment_gateway\ ' . $model_name);
        $status = $model_full_path::payment_status($identifier, $request->all());

        if ($status === true) {
            $payment_details = [
                'payment_details' => [
                    'uuid' => $request->uuid,
                ],
            ];

            if ($this->paidCharge($payment_details, $request) == true) {
                return response()->json(['success' => true, 'message' => 'Payment success'], 200);
            }

            return response()->json(['success' => false, 'message' => 'Payment failed'], 400);
        }

        return response()->json(['success' => false, 'message' => 'Payment failed'], 400);
    }

    public function verify_card(Request $request)
    {
        $card_code = $request->card_code;
        $coupon = Coupon::where('code', $card_code)->first();

        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'Card not found']);
        }

        if ($coupon->type != 'recharge') {
            return response()->json(['success' => false, 'message' => 'Card is not a recharge card']);
        }

        if (!$coupon->isValid()) {
            return response()->json(['success' => false, 'message' => 'Card is not valid']);
        }

        if (!$coupon->canBeUsedForUser($request->user()->id)) {
            return response()->json(['success' => false, 'message' => 'Card is not for you']);
        }

        $this->saveCharge('card', $coupon->value, '1');
        CouponLog::create([
            'coupon_id' => $coupon->id,
            'user_id' => $request->user()->id,
        ]);
        $request->user()->increment('wallet', $coupon->value);

        return response()->json([
            'success' => true,
            'amount' => $coupon->value,
            'message' => 'Card verified successfully',
        ]);
    }

    private function paidCharge($payment_details, $request)
    {
        $log = WalletLog::where('uuid', $payment_details['payment_details']['uuid'])->first();

        if ($log) {
            if ($log->status == false) {
                $log->status = '1';
                $log->payment_id = $request->payment_id;
                $log->save();
                $log->student->increment('wallet', $log->balance);
            }

            return true;
        }

        return false;
    }

    private function saveCharge($identifier, $balance, $status = '0')
    {
        $uuid = Str::uuid();
        WalletLog::create([
            'student_id' => auth()->user()->id,
            'added_by' => auth()->user()->id,
            'uuid' => $uuid,
            'status' => $status,
            'type' => $identifier,
            'balance' => $balance,
        ]);

        return $uuid;
    }
}
