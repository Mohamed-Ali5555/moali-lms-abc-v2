<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\HomeController;
use App\Http\Controllers\Api\v1\CategoryController;
use App\Http\Controllers\Api\v1\CourseController;
use App\Http\Controllers\Api\v1\ProfileController;
use App\Http\Controllers\Api\v1\StudentController;
use App\Http\Controllers\Api\v1\WishlistController;
use App\Http\Controllers\Api\v1\CartController;
use App\Http\Controllers\Api\v1\PlayerController;
use App\Http\Controllers\Api\v1\PurchaseController;
use App\Http\Controllers\Api\v1\UserController;
use App\Http\Controllers\Api\v1\QuizController;
use App\Http\Controllers\Api\v1\WalletController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // ── Auth (public) ──────────────────────────────────────────────
    Route::post('login', [AuthController::class, 'login']);
    // Route::post('signup', [AuthController::class, 'signup']);
    Route::post('register', [AuthController::class, 'signup']);
    Route::post('forgot/password', [AuthController::class, 'forgot_password']);
    Route::get('register-categories', [AuthController::class, 'registerCategories']);

    // ── Public content ─────────────────────────────────────────────
    Route::get('books', [HomeController::class, 'getBooks']);
    Route::get('book-details/{id}', [HomeController::class, 'getBookDetails']);
    Route::get('theme-features', [HomeController::class, 'getFeatures']);
    Route::get('theme-settings', [HomeController::class, 'getThemeSettings']);

    Route::get('categories', [CategoryController::class, 'allCategories']);
    Route::get('category-details/{id}', [CategoryController::class, 'category_details']);

    Route::get('courses', [CourseController::class, 'allCourses']);
    Route::get('course-details/{id}', [CourseController::class, 'courseDetails']);
    // Route::get('courses-filter', [CourseController::class, 'filter_course']);
    Route::get('sections', [CourseController::class, 'sections']);

    Route::match(['get', 'post'], 'wallet/success/{identifier}', [WalletController::class, 'payment_success']);

    // ── Authenticated routes ───────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);

        // Profile
        Route::get('profile', [ProfileController::class, 'show']);
        Route::post('update-profile/{user_id}', [ProfileController::class, 'update']);
        // Route::post('profile-picture', [ProfileController::class, 'updateProfilePicture']);
        Route::post('password', [UserController::class, 'update_password']);

        // Student
        Route::get('my-courses', [StudentController::class, 'myCourses']);
        Route::get('my-books', [StudentController::class, 'myBooks']);
        // Purchase history
        Route::get('my-purchase', [StudentController::class, 'mypurchaseHistory']);
        Route::get('my-invoice/{id}', [StudentController::class, 'myinvoice']);

        // Wallet
        Route::get('my-wallet', [StudentController::class, 'mywallet']);
        Route::get('wallet/show_payment_gateway_by_ajax/{identifier}/{balance}', [WalletController::class, 'show_payment_gateway_by_ajax']);
        Route::post('wallet/verify_card', [WalletController::class, 'verify_card']);


         // Quiz
         Route::get('quiz/{quiz_id}', [QuizController::class, 'getQuiz']);
         Route::get('quiz/{quiz_id}/questions', [QuizController::class, 'loadQuestions']);
         Route::post('start-quiz/{quiz_id}/start', [QuizController::class, 'startQuiz']);
         Route::post('submit-quiz/{quiz_id}/submit', [QuizController::class, 'submitQuiz']);
       
       // not completed
         Route::post('quiz/save-answer', [QuizController::class, 'saveAnswer']);
         Route::get('quiz/{quiz_id}/result/{submission_id}', [QuizController::class, 'getQuizResult']);
         Route::get('quiz/{quiz_id}/submissions', [QuizController::class, 'getQuizSubmissions']);
 
   


        // Wishlist
        // Route::get('wishlist', [WishlistController::class, 'index']);
        // Route::get('wishlist/toggle/{course_id}', [WishlistController::class, 'toggle']);

        // Cart
        Route::get('cart', [CartController::class, 'index']);
        Route::post('cart/{id}', [CartController::class, 'store']);
        Route::delete('cart/{id}', [CartController::class, 'delete']);
        Route::post('cart/{cartId}/quantity', [CartController::class, 'updateQuantity']);



        // Course player
        Route::get('player/course/{slug}/{lesson_id?}', [PlayerController::class, 'coursePlayer']);
        Route::post('player/watch-history', [PlayerController::class, 'setWatchHistory']);
        Route::post('player/watch-duration', [PlayerController::class, 'updateWatchDuration']);

        // Coupons
        Route::post('coupons/validate', [CouponController::class, 'validateCoupon']);
        Route::get('coupons/info', [CouponController::class, 'getCouponInfo']);
        Route::post('coupons/recharge', [CouponController::class, 'applyRechargeCoupon']);
        Route::post('coupons/discount', [CouponController::class, 'applyDiscountCoupon']);
        Route::post('coupons/payment', [CouponController::class, 'applyPaymentCoupon']);
        Route::post('coupons/transfer', [CouponController::class, 'transferCoupon']);
        Route::get('coupons/user', [CouponController::class, 'getUserCoupons']);

   
       
        // Legacy endpoints (still used by older mobile builds)
        // Route::get('top-courses', [ApiController::class, 'top_courses']);
        // Route::get('sub-categories/{id}', [ApiController::class, 'sub_categories']);
        // Route::get('category-wise-course', [ApiController::class, 'category_wise_course']);
        // Route::get('category-subcategory-wise-course', [ApiController::class, 'category_subcategory_wise_course']);
        Route::get('languages', [ApiController::class, 'languages']);
        // Route::get('courses-by-search', [ApiController::class, 'courses_by_search_string']);
        // Route::get('course-details-by-id', [ApiController::class, 'course_details_by_id']);
        Route::post('account-disable', [ApiController::class, 'account_disable']);
        Route::get('save-course-progress', [ApiController::class, 'save_course_progress']);
        Route::get('zoom/settings', [ApiController::class, 'zoom_settings']);
        Route::get('zoom/meetings', [ApiController::class, 'live_class_schedules']);
        Route::get('payment/{token}', [ApiController::class, 'payment']);
        Route::get('token', [ApiController::class, 'token']);
        Route::get('free-course-enroll/{course_id}', [ApiController::class, 'free_course_enroll']);
        Route::get('cart-tools', [ApiController::class, 'cart_tools']);
    });
});

// ── Backward-compatible routes (without v1 prefix) ─────────────────
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/forgot_password', [AuthController::class, 'forgot_password']);

Route::get('/books', [HomeController::class, 'getBooks']);
Route::get('/books-details/{id}', [HomeController::class, 'getBookDetails']);
Route::get('/theme-feature', [HomeController::class, 'getFeatures']);
Route::get('/theme-settings', [HomeController::class, 'getThemeSettings']);
Route::get('/all-categories', [CategoryController::class, 'allCategories']);
Route::get('/category_details/{id}', [CategoryController::class, 'category_details']);
Route::get('/all-Courses', [CourseController::class, 'allCourses']);
Route::get('/course-details/{id}', [CourseController::class, 'courseDetails']);
Route::get('/filter_course', [CourseController::class, 'filter_course']);
Route::match(['get', 'post'], '/wallet/success/{identifier}', [WalletController::class, 'payment_success']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/coupons/validate', [CouponController::class, 'validateCoupon']);
    Route::get('/coupons/info', [CouponController::class, 'getCouponInfo']);
    Route::post('/coupons/recharge', [CouponController::class, 'applyRechargeCoupon']);
    Route::post('/coupons/discount', [CouponController::class, 'applyDiscountCoupon']);
    Route::post('/coupons/payment', [CouponController::class, 'applyPaymentCoupon']);
    Route::post('/coupons/transfer', [CouponController::class, 'transferCoupon']);
    Route::get('/coupons/user', [CouponController::class, 'getUserCoupons']);

    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/top_courses', [ApiController::class, 'top_courses']);
    Route::get('/all_categories', [ApiController::class, 'all_categories']);
    Route::get('/category_details', [ApiController::class, 'category_details']);
    Route::get('/sub_categories/{id}', [ApiController::class, 'sub_categories']);
    Route::get('/category_wise_course', [ApiController::class, 'category_wise_course']);
    Route::get('/category_subcategory_wise_course', [ApiController::class, 'category_subcategory_wise_course']);
    Route::get('/my_wishlist', [ApiController::class, 'my_wishlist']);
    Route::get('/toggle_wishlist_items', [ApiController::class, 'toggle_wishlist_items']);
    Route::get('/languages', [ApiController::class, 'languages']);
    Route::get('/courses_by_search_string', [ApiController::class, 'courses_by_search_string']);
    Route::get('/my_courses', [ApiController::class, 'my_courses']);
    // Route::get('/sections', [ApiController::class, 'sections']);
    Route::get('/course_details_by_id', [ApiController::class, 'course_details_by_id']);
    Route::post('/update_password', [UserController::class, 'update_password']);
    Route::post('/update_userdata', [ApiController::class, 'update_userdata']);
    Route::post('/account_disable', [ApiController::class, 'account_disable']);
    Route::get('/cart_list', [ApiController::class, 'cart_list']);
    Route::get('/toggle_cart_items', [ApiController::class, 'toggle_cart_items']);
    Route::get('/save_course_progress', [ApiController::class, 'save_course_progress']);
    Route::get('zoom/settings', [ApiController::class, 'zoom_settings']);
    Route::get('zoom/meetings', [ApiController::class, 'live_class_schedules']);
    Route::get('payment/{token}', [ApiController::class, 'payment']);
    Route::get('token', [ApiController::class, 'token']);
    Route::get('free_course_enroll/{course_id}', [ApiController::class, 'free_course_enroll']);
    Route::get('cart_tools', [ApiController::class, 'cart_tools']);

    Route::get('/quiz/{quiz_id}', [QuizController::class, 'getQuiz']);
    Route::get('/quiz/{quiz_id}/questions', [QuizController::class, 'loadQuestions']);
    Route::post('/quiz/{quiz_id}/start', [QuizController::class, 'startQuiz']);
    Route::post('/quiz/{quiz_id}/submit', [QuizController::class, 'submitQuiz']);
    Route::post('/quiz/save-answer', [QuizController::class, 'saveAnswer']);
    Route::get('/quiz/{quiz_id}/result/{submission_id}', [QuizController::class, 'getQuizResult']);
    Route::get('/quiz/{quiz_id}/submissions', [QuizController::class, 'getQuizSubmissions']);


});
