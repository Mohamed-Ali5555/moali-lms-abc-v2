<?php

namespace App\Http\Controllers\student;

use App\Http\Controllers\Controller;
use App\Models\FileUploader;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\Wallet\App\Models\WalletLog;

class MyWalletController extends Controller
{
    public function index()
    {
        $page_data['user_wallets'] = WalletLog::where('student_id',auth()->user()->id)->paginate(15);
        $view_path = 'frontend.' . get_frontend_settings('theme') . '.student.my_wallet.index';
        return view($view_path, $page_data);
    }


}
