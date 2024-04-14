<?php

use App\Models\Buyer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\SendOtpController;
use App\Http\Controllers\Api\RestaurantController;
use App\Http\Controllers\Api\RestaurantTableBookingController;
use App\Http\Controllers\Api\Auth\VerifyOtpController;
use App\Http\Controllers\Api\Auth\ProfileController;
use App\Helpers\CommonHelper;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\SubscriptionPlanController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\QueryController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
|--------------------------------------------------------------------------
| Login-Signup Routes
|--------------------------------------------------------------------------
*/
Route::post('/mobile-register', [RegisterController::class, 'mobileRegister']);
Route::post('/register', [RegisterController::class, 'store']);
Route::post('/send-otp', [SendOtpController::class, 'send']);
Route::post('/verify-otp', [VerifyOtpController::class, 'verifyOtp']);


/*
|--------------------------------------------------------------------------
| Without Auth Routes
|--------------------------------------------------------------------------
*/
Route::get('/pages/{page_slug}', [PageController::class, 'get_page_detail']);

Route::post('/restaurant/detail', [RestaurantController::class, 'restaurant_post']);
Route::get('/restaurant/tags', [TagController::class, 'tags']);

Route::middleware(['auth:sanctum','check_account_status:web'])->group(function () {
    
    Route::get('/logout', [LoginController::class,'logout']);
    Route::get('/myprofile', [ProfileController::class,'myprofile']);
    Route::post('/updateprofile', [ProfileController::class,'update_profile']);
    Route::post('/user/profile', [ProfileController::class,'profile']);
    Route::post('/user/account/deactivate', [ProfileController::class,'account_deactivate']);
    
    /*
    |--------------------------------------------------------------------------
    | Auth Restaurant Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/restaurant/tags/listing', [RestaurantController::class, 'restaurant_tags_listing']);
    Route::get('/restaurant/listing', [RestaurantController::class, 'restaurant_listing']);
    Route::get('/restaurant/detail', [RestaurantController::class, 'restaurant_detail']);
    Route::post('/restaurant/wishlist', [RestaurantController::class, 'restaurant_wishlist']);
    Route::post('/restaurant/availability/time/slots', [RestaurantController::class, 'availability_time_slots']);
    Route::get('/my/restaurant/detail', [RestaurantController::class, 'restaurant_get']);
    Route::get('/my/favorite/restaurant', [RestaurantController::class, 'my_favorite_restaurant']);
    Route::post('/restaurant/timing', [RestaurantController::class, 'restaurant_timing']);
    Route::get('/featured/restaurant/list', [RestaurantController::class, 'featured_restaurant_list']);

    Route::get('/restaurant/tags/listingnew', [RestaurantController::class, 'restaurant_tags_listingnew']);
    /*
    |--------------------------------------------------------------------------
    | Restaurant Table Booking Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/restaurant/table/booking/detail', [RestaurantTableBookingController::class, 'restaurant_table_booking_detail']);
    Route::post('/restaurant/table/booking', [RestaurantTableBookingController::class, 'restaurant_table_booking']);
    Route::get('business/restaurant/table/booking/list/all', [RestaurantTableBookingController::class, 'business_restaurant_table_booking_list_all']);
    Route::get('business/restaurant/table/booking/list', [RestaurantTableBookingController::class, 'business_restaurant_table_booking_list']);
    Route::post('business/booking/accept/reject', [RestaurantTableBookingController::class, 'accept_reject_booking']);
    Route::get('customer/restaurant/table/booking/list', [RestaurantTableBookingController::class, 'customer_restaurant_table_booking_list']);

    /*
    |--------------------------------------------------------------------------
    | PLANS
    |--------------------------------------------------------------------------
    */
    Route::get('/subscription/plans',[SubscriptionController::class,'plans']);
    Route::get('my/subscription/plan',[SubscriptionController::class,'my_subscription_plan']);
    Route::post('/subscription/plans/buy',[SubscriptionController::class,'subscription_plan_buy']);
    // Route::get('/subscription/plans',[SubscriptionPlanController::class,'subscription_plans']);
    Route::get('/featured/subscription/availability',[SubscriptionPlanController::class,'featured_subscription_availability']);
    
    /*
    |--------------------------------------------------------------------------
    | Query
    |--------------------------------------------------------------------------
    */
    Route::post('send/query',[QueryController::class,'send_query']);
});
