<?php

use Illuminate\Support\Facades\Route;
use Modules\BookStore\App\Http\Controllers\BookStoreController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group([], function () {
    Route::resource('bookstore', BookStoreController::class)->names('bookstore');
    Route::post('book/sort', [BookStoreController::class,'book_sort'])->name('bookstore.sort');

});
