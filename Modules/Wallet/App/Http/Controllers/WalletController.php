<?php

namespace Modules\Wallet\App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Wallet\App\Http\Requests\WalletRequest;
use Modules\Wallet\App\Http\Requests\WalletCategoryRequest;
use Modules\Wallet\App\Http\Services\WalletService;
use Modules\Wallet\App\Http\Resources\WalletResource;
use App\Models\User;
use App\Models\Category;

class WalletController extends Controller
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }
    public function index(Request $request)
    {
        $querySearch = null;
        if($request->has('search')){
            $querySearch = $request->search;
        }
        $wallets =  $this->walletService->getWallets($querySearch);
        return view('wallet::wallet.index', compact('wallets'));
    }

    public function store(WalletRequest $request)
    {
        $wallet = $this->walletService->createWallet($request->validated());
        return redirect()->route('admin.wallet.index')->with('success', 'balance created successfully.');
    }

    public function store_decreas(WalletRequest $request)
    {
        // $wallet =

       return $this->walletService->decreasWallet($request->validated());
        // return  $wallet ;
        return redirect()->route('admin.wallet.index')->with('success', 'balance decreasd successfully.');
    }

    public function destroyCategoryWallet($id)
    {
        $this->walletService->deleteWalletCategory($id);
        return redirect()->route('admin.wallet_category.index')->with('success', 'balance deleted successfully.');
    }

    public function destroy($id)
    {
        $this->walletService->deleteWallet($id);
        return redirect()->route('admin.wallet.index')->with('success', 'balance deleted successfully.');
    }

    public function wallet_category(Request $request)
    {
        $querySearch = null;
        if($request->has('search')){
            $querySearch = $request->search;
        }
        $wallet_categories = $this->walletService->getAllWalletsCategory($querySearch);
        $categories = Category::get();
        return view('wallet::wallet_category.index',compact('categories','wallet_categories'));

    }

    public function wallet_category_store(WalletCategoryRequest $request){
        $wallet_category = $this->walletService->createWalletCategory($request->validated());
        if($wallet_category['status']){
            return redirect()->route('admin.wallet_category.index')->with('success', $wallet_category['message']);
        }else{
            return redirect()->route('admin.wallet_category.index')->with('error', $wallet_category['message']);
        }
        return redirect()->route('admin.wallet_category.index')->with('success', 'balance for category created successfully.');
    }

    public function changeStatus($id){

        $row = $this->walletService->changeStatus($id);
        return redirect()->back()->with('success','status changed successfuly');
    }
}
