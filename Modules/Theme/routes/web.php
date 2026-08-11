<?php

use Illuminate\Support\Facades\Route;
use Modules\Theme\App\Http\Controllers\ThemeController;
use Modules\Theme\App\Http\Controllers\HomeController;
use Modules\Theme\App\Http\Controllers\CategoryController;
use Modules\Theme\App\Http\Controllers\CourseController;
use Modules\Theme\App\Http\Controllers\SettingController;
use Modules\Theme\App\Http\Controllers\Auth\AuthController;
use Modules\Theme\App\Http\Controllers\CartController;
use Modules\Theme\App\Http\Controllers\PaymentController;

use Modules\Theme\App\Http\Controllers\Auth\PasswordResetLinkController;
use Modules\Theme\App\Http\Controllers\Auth\NewPasswordController;
use Modules\Theme\App\Http\Controllers\ContactController;

use Modules\Theme\App\Http\Controllers\BootcampController;

use Modules\Theme\App\Http\Controllers\student\BecomeInstructorController;
use Modules\Theme\App\Http\Controllers\student\BlogCommentController;
use Modules\Theme\App\Http\Controllers\student\BlogController;
use Modules\Theme\App\Http\Controllers\student\BootcampPurchaseController;
use Modules\Theme\App\Http\Controllers\student\InvoiceController;
use Modules\Theme\App\Http\Controllers\student\LiveClassController;
use Modules\Theme\App\Http\Controllers\student\MessageController;
use Modules\Theme\App\Http\Controllers\student\MyBootcampsController;
use Modules\Theme\App\Http\Controllers\student\MyCoursesController;
use Modules\Theme\App\Http\Controllers\student\MyProfileController;
use Modules\Theme\App\Http\Controllers\student\MyWalletController;
use Modules\Theme\App\Http\Controllers\student\MyBooksController;
use Modules\Theme\App\Http\Controllers\student\MyPerformanceController;

use Modules\Theme\App\Http\Controllers\student\MyTeamPackageController;
use Modules\Theme\App\Http\Controllers\student\OfflinePaymentController;
use Modules\Theme\App\Http\Controllers\student\PurchaseController;
use Modules\Theme\App\Http\Controllers\student\QuizController;
use Modules\Theme\App\Http\Controllers\student\ReviewController;
use Modules\Theme\App\Http\Controllers\student\WishListController;
use Modules\Theme\App\Http\Controllers\student\TutorBookingController;
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
Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::group(['as'=>'theme.'], function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('category/{id}', [CategoryController::class, 'index'])->name('category');
    Route::get('course-details/{id}', [CourseController::class, 'course_details'])->name('course.details');
    Route::get('book-details/{id}', [CourseController::class, 'book_details'])->name('book.details');



    /// cart
    Route::any('cart-index',[CartController::class, 'index'])->name('cart');
    Route::get('cart/destroy/{id}',[CartController::class, 'delete'])->name('cart.delete');
    Route::post('cart/save/{id}',[CartController::class, 'store'])->name('cart.store');
    Route::post('cart/update',[CartController::class, 'update'])->name('cart.update');


    Route::post('books/{id}/update-quantity',[CartController::class, 'updateQuantity'])->name('cartBooks.updateQuantity');



    /// auth controllers
    Route::get('login', [AuthController::class, 'show_login'])->name('show_login')->middleware('guest');
    Route::post('login', [AuthController::class, 'login'])->name('login')->middleware('guest');


    Route::get('register', [AuthController::class, 'show_register'])->name('show_register')->middleware('guest');
    Route::post('register', [AuthController::class, 'register'])->name('register')->middleware('guest');

    // rest password
    Route::get('forgot/password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot/password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('reset/password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset/password', [NewPasswordController::class, 'store'])->name('password.store');

    // end rest password

    // terms and conditions
    Route::get('terms-condition', [HomeController::class, 'termsCondition'])->name('terms.condition');
   // bootacmp
   Route::get('get-bootcamp/{category?}', [BootcampController::class, 'index'])->name('bootcamps');
   Route::get('get-bootcamp/{slug}', [BootcampController::class, 'show'])->name('bootcamp.details');

    // about us
    Route::get('about-us', [HomeController::class, 'aboutUs'])->name('about.us');

    // accreditation
    Route::get('accreditation', [HomeController::class, 'accreditation'])->name('accreditation');

    // contact us
    Route::get('contact-us', [ContactController::class, 'index'])->name('contact.us');
    Route::post('contact-us', [ContactController::class, 'store'])->name('contact.store');

});


Route::controller(PaymentController::class)->middleware('auth')->group(function () {
    Route::get('payment', 'index')->name('payment');
    Route::get('payment/show_payment_gateway_by_ajax/{identifier}', 'show_payment_gateway_by_ajax')->name('payment.show_payment_gateway_by_ajax');
    Route::get('payment/success/{identifier?}', 'payment_success')->name('payment.success');
    Route::get('payment/create/{identifier}', 'payment_create')->name('payment.create');

    // free course
    Route::get('payment/success/free/{id?}/type', 'payment_successFree')->name('payment.successFree');

    //handleCallbackfawry
    Route::post('fawry/callback','fawryHandleCallback')->name('fawry.callback')->withoutMiddleware('auth');
    Route::post('paymob/callback','paymobHandleCallback')->name('paymob.callback')->withoutMiddleware('auth');
    Route::post('payment/verify_card','verify_card')->name('payment.verify_card');
});


Route::middleware(['auth'])->as('theme.')->group(function () {
    // my profile routes
    Route::controller(MyWalletController::class)->group(function () {
        Route::get('mywallet', 'index')->name('my.wallet');
        Route::get('wallet/show_payment_gateway_by_ajax/{identifier}/{balance}', 'show_payment_gateway_by_ajax')->name('wallet.show_payment_gateway_by_ajax');
        Route::get('wallet/success/{identifier?}', 'payment_success')->name('payment.wallet.success')->withoutMiddleware('auth');

        //handleCallbackfawry
        Route::post('wallet/callback/fawry','fawryHandleCallback')->name('wallet.fawry.callback')->withoutMiddleware('auth');
        Route::post('wallet/callback/paymob','paymobHandleCallback')->name('wallet.paymob.callback')->withoutMiddleware('auth');
        Route::post('wallet/verify_card','verify_card')->name('wallet.verify_card');
    });


    // my wallet routes
    Route::controller(MyProfileController::class)->group(function () {
        Route::get('myprofile', 'index')->name('my.profile');
        Route::post('myprofile/update/{user_id}', 'update')->name('update.profile');
        Route::post('update/profile-picture', 'update_profile_picture')->name('update.profile.picture');
    });
    // my wishlist routes
    Route::controller(WishListController::class)->group(function () {
        Route::get('mywishlist', 'index')->name('wishlist');
        Route::get('mytoggleWishItem/{course_id?}', 'toggleWishItem')->name('toggleWishItem');
    });

    // my course routes
    Route::controller(MyCoursesController::class)->group(function () {
        Route::get('mycourses', 'index')->name('my.courses');
    });
   Route::controller(MyBooksController::class)->group(function () {
        Route::get('mybooks', 'index')->name('my.books');
        Route::get('mybooks/{id}/view', 'view')->name('my.books.view');
        Route::get('mybooks/{id}/file', 'file')->name('my.books.file');
    });
    Route::controller(MyPerformanceController::class)->group(function () {
        Route::get('myperformance', 'index')->name('my.performance');
    });

    // quiz routes
    // Route::controller(QuizController::class)->group(function () {
    //     Route::post('quiz/submit/{id}', 'quiz_submit')->name('quiz.submit');
    //     Route::get('load/quiz/result/', 'load_result')->name('load.quiz.result');
    //     Route::get('load/quiz/questions/', 'load_questions')->name('load.quiz.questions');
    // });

    // purchase routes
    Route::controller(PurchaseController::class)->group(function () {
        Route::get('mypurchase/course/{course_id}', 'purchase_course')->name('purchase.course');
        Route::post('mypayout', 'payout')->name('payout');
        Route::get('mypurchase-history', 'purchase_history')->name('purchase.history');
        Route::get('myinvoice/{id}', 'invoice')->name('invoice');
    });



    // review routes
    Route::controller(ReviewController::class)->group(function () {
        Route::post('myreview/store', 'store')->name('review.store');
        Route::get('myreview/edit/', 'edit')->name('review.edit');
        Route::get('myreview/delete/{id}', 'delete')->name('review.delete');
        Route::post('myreview/update/{id}', 'update')->name('review.update');
        Route::get('myreview/like/{id}', 'like')->name('review.like');
        Route::get('myreview/dislike/{id}', 'dislike')->name('review.dislike');
    });

    // blog
    Route::controller(BlogController::class)->middleware('blog.visibility')->group(function () {
        Route::get('/myblog-like', 'blog_like')->name('blog.like');
    });

    // blog comment
    Route::controller(BlogCommentController::class)->middleware('blog.visibility')->group(function () {
        Route::post('/myblog/comment/store', 'store')->name('blog.comment.store');
        Route::get('/myblog/comment/delete/{id}', 'delete')->name('blog.comment.delete');
        Route::post('/myblog/comment/update/{id}', 'update')->name('blog.comment.update');
    });

    // message
    Route::controller(MessageController::class)->group(function () {
        Route::get('/mymessage', 'index')->name('message');
        Route::post('/mymessage/store', 'store')->name('message.store');
        Route::get('/mymessage/fetch', 'fetch_message')->name('message.fetch');
        Route::post('/mymessage/search/student', 'search_student')->name('search.student');
        Route::get('/mymessage/inbox/{user_id}', 'inbox')->name('message.inbox');
    });

    // become instructor
    // Route::controller(BecomeInstructorController::class)->group(function () {
    //     Route::get('/become-an-instructor', 'index')->name('become.instructor');
    //     Route::post('/become-an-instructor/store', 'store')->name('become.instructor.store');
    // });

    // live class
    // Route::controller(LiveClassController::class)->group(function () {
    //     Route::get('live-class/join/{id}', 'live_class_join')->name('live.class.join');
    // });

    // my bootcamp routes
    Route::controller(MyBootcampsController::class)->group(function () {
        Route::get('mybootcamps/', 'index')->name('my.bootcamps');
        Route::get('mybootcamps/details/{slug?}', 'show')->name('my.bootcamp.details');
        Route::get('mybootcamps/invoice/{id}', 'invoice')->name('my.bootcamp.invoice');
        Route::get('mybootcamp/live/class/join/{topic}', 'join_class')->name('bootcamp.live.class.join');
        Route::get('mybootcamp/resource/download/{id}', 'download')->name('bootcamp.resource.download');
        Route::get('mybootcamp/resource/play/{file}', 'play')->name('bootcamp.resource.play');
    });

    // purchase bootcamp routes
    Route::controller(BootcampPurchaseController::class)->group(function () {
        Route::get('mypurchase/bootcamp/{id}', 'purchase')->name('purchase.bootcamp');
        Route::get('mybootcamp/purchase/history', 'purchase_history')->name('bootcamp.purchase.history');
        Route::get('mybootcamp/invoice/{id}', 'invoice')->name('bootcamp.invoice');
    });

    // my team packages
    Route::controller(MyTeamPackageController::class)->group(function () {
        Route::get('myteam-packages/', 'index')->name('my.team.packages');
        Route::get('myteam-packages/details/{slug}', 'show')->name('my.team.packages.details')
            ->middleware('record.exists:team_training_packages,slug');
        Route::get('myteam-packages/search/members/{package_id?}', 'search_members')->name('search.package.members');
        Route::get('myteam-packages/{action}/members', 'member_action')->name('my.team.packages.members.action');
        Route::get('mypurchase/team-package/{id}', 'purchase')->name('purchase.team.package');
        Route::get('myteam-packages/invoice/{id}', 'invoice')->name('team.package.invoice')
            ->middleware('record.exists:team_package_purchases,id');
    });

    // tutor booking
    Route::controller(TutorBookingController::class)->group(function () {
        Route::get('mybookings', 'my_bookings')->name('my_bookings');
        Route::get('mybooking-invoice/{id}', 'booking_invoice')->name('booking_invoice');
        Route::get('mypurchase/schedule/{id}', 'purchase')->name('purchase_schedule');
        Route::get('mybookings/tution-class/join/{booking_id}', 'join_class')->name('tution_class.join');

        Route::post('mytutor-review', 'tutor_review')->name('tutor_review');
    });

});




Route::name('admin.')->prefix('admin')->middleware('admin')->group(function () {
    Route::controller(SettingController::class)->group(function () {

        // frontend new theme settings
        Route::get('theme_settings', 'settings')->name('theme.settings');
        Route::any('theme_settings/store/{param1}/{id?}', 'settings_store')->name('theme.settings.store');
        // end frontend new theme setting


        Route::get('theme-social', 'social')->name('theme.social');
        Route::get('create-social', 'create_social')->name('theme.social.create');
        Route::any('theme-social/store', 'social_store')->name('theme.social.store');
        Route::get('theme-social/delete/{id}', 'social_delete')->name('theme.social.delete');


        Route::get('theme-feature', 'feature')->name('theme.feature');
        Route::get('create-feature', 'create_feature')->name('theme.feature.create');
        Route::any('theme-feature/store', 'feature_store')->name('theme.feature.store');
        Route::get('theme-feature/delete/{id}', 'feature_delete')->name('theme.feature.delete');
         Route::get('theme-feature/activation/{id}','activeFeature')->name('theme.feature.activeFeature');

        Route::get('theme-legal', 'legal')->name('theme.legal');
        Route::post('theme-legal/store', 'legal_store')->name('theme.legal.store');

       });
    });
