<?php

namespace App\Models\payment_gateway;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



//for paypal
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Wallet extends Model
{
    use HasFactory;

    public static function payment_status($identifier, $transaction_keys = [])
    {
        $payment_gateway = DB::table('payment_gateways')->where('identifier', $identifier)->first();
        $payment_details = session('payment_details');
        $balance = auth()->user()->wallet;
        if ($balance >= $payment_details['payable_amount']) {
            auth()->user()->decrement('wallet', $payment_details['payable_amount']);
            return true;
        } else {
            return "رصيد المحفظة لا يكفي لعملية الشراء";
        }
    }
}
