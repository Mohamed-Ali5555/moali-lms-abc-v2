<?php

use Illuminate\Support\Facades\Route;
use Modules\BankQuestions\App\Http\Controllers\QuizController;
use Modules\BankQuestions\App\Http\Controllers\QuestionController;
use Modules\BankQuestions\App\Http\Controllers\BankquestionCategoryController;

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

Route::name('admin.')->prefix('admin')->middleware('admin')->group(function () {

    Route::resource('bank-questions-category', BankquestionCategoryController::class)->names('category.bank.questions');
    Route::post('bank-questions-category/{id}', [BankquestionCategoryController::class,'update'])->name('category.bank.questions.update');
    // Route::resource('bank-quizs', QuizController::class)->names('bank.quizs');
    // Route::post('bank-questions/update/{id}'[], 'update')->name('bank.quizs.update');

    Route::controller(QuizController::class)->group(function () {
        Route::get('bank-quizs', 'index')->name('bank.quizs.index');
        Route::get('bank-quizs/edit/{id}', 'edit')->name('bank.quizs.edit');
        Route::get('bank-quizs/show/{id}', 'show')->name('bank.quizs.show');
        Route::get('bank-quizs/create/', 'create')->name('bank.quizs.create');
        Route::post('bank-quizs/store', 'store')->name('bank.quizs.store');
        Route::get('bank-quizs/destroy/{id}', 'destroy')->name('bank.quizs.destroy');
        Route::post('bank-quizs/update/{id}', 'update')->name('bank.quizs.update');
    });


    Route::controller(QuestionController::class)->group(function () {
        Route::get('bank-questions', 'index')->name('bank.question.index');
        Route::post('bank-questions/store', 'store')->name('bank.question.store');
        // choose route
        Route::post('bank-questions/choose', 'choose')->name('bank.question.choose');

        Route::get('bank-questions/delete/{quiz}/{id}', 'delete')->name('bank.question.delete');
        Route::get('bank-questions/deleteQuestions/{id}', 'deleteQuestions')->name('bank.question.deleteQuestions');
        Route::post('bank-questions/update/{id}', 'update')->name('bank.question.update');
        Route::get('bank-questions/sort/', 'sort')->name('bank.question.sort');
        Route::get('bank-questions/type/', 'load_type')->name('bank.question.type');
        Route::get('QuizesUsingCategory/{id}', 'QuizesUsingCategory')->name('bank.quizs.using.category');
    });
});
