<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\AdminLoginController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminDashboardController;
use App\Models\Business;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendTrialExpirationMail;
use GuzzleHttp\Psr7\Request;

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

Auth::routes();

// admin login
Route::middleware([])->group(function () {
    Route::get('/login/admin', [LoginController::class,'showAdminLoginForm'])->name('admin.login');
    Route::get('/login/admin/forget-password', 'FrontendController@showAdminForgetPasswordForm')->name('admin.forget.password');
    Route::get('/login/admin/reset-password/{user}/{token}', 'FrontendController@showAdminResetPasswordForm')->name('admin.reset.password');
    Route::post('/login/admin/reset-password', 'FrontendController@AdminResetPassword')->name('admin.reset.password.change');
    Route::post('/login/admin/forget-password', 'FrontendController@sendAdminForgetPasswordMail');
    Route::any('/logout/admin', [AdminDashboardController::class,'adminLogout'])->name('admin.logout');
    Route::post('/login/admin', [LoginController::class,'adminLogin']);
});

//users routes
Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
// Route::get('/', function () {return view('welcome');});
// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


//admin routes
Route::group([
               'prefix'     => 'admins',
               'as'         => 'admin.',
               'namespace'  => 'App\Http\Controllers\Admin',
               'middleware' => ['auth','AdminAuth']
            ],
    function(){
    
    Route::get('/', function () {return view('admin.pages.dashboard');})->name('index'); 
    Route::resource('users',UserController::class);
    Route::resource('vendors',VendorController::class);
    Route::resource('category',CategoryController::class);
    Route::resource('product',ProductController::class);
    Route::resource('sliders',SliderController::class);
    Route::get('product/{id}/images',[App\Http\Controllers\Admin\ProductController::class,'product_images'])->name('product.images');
    Route::delete('product/{product_id}/images/{image_id}',[App\Http\Controllers\Admin\ProductController::class,'product_images_delete'])->name('product.images.destroy');
    Route::post('users/changeActiveStatus',[App\Http\Controllers\Admin\UserController::class,'change_active_status'])->name('users.change_active_status');
    
    //Dropzone
    Route::get('slider-images','SliderController@slider_images')->name('slider-images');
    //** admin change password **//
    Route::group(['as' => 'settings.'],function(){

        Route::get('/change-password','SettingController@changePassword')->name('change-password');
        Route::post('/reset-password','SettingController@resetPassword')->name('reset-password');
        Route::match(['get','post'],'/slider','SettingController@slider')->name('slider');

    });

});

Route::group([
    'prefix'     => 'admins',
    'as'         => 'admin.',
    'namespace'  => 'App\Http\Controllers',
    'middleware' => ['auth','AdminAuth']
 ],
function(){

    /*businness routes*/
    Route::group(['namespace' => 'Customer'],function(){
        Route::resource('customer', 'CustomerController');
        Route::get('customer_datatable', 'CustomerController@customer_datatable')->name('customer.datatable');
        Route::post('customer/change-profile-verification-status/{customer}','CustomerController@change_profile_verification_status')->name('customer.change-profile-verification-status');     
    
    });

    /*businness routes*/
    Route::group(['namespace' => 'Business'],function(){
        Route::resource('business', 'BusinessController');
        Route::get('business_datatable', 'BusinessController@business_datatable')->name('business.datatable');
        Route::post('business/change-profile-verification-status/{business}','BusinessController@change_profile_verification_status')->name('business.change-profile-verification-status');     
    
    });
});


Route::get('pay/{user_id?}', 'App\Http\Controllers\PayController@pay');
Route::post('/payments/pay', 'App\Http\Controllers\PaymentController@pay')->name('pay');
Route::get('/payments/approval', 'App\Http\Controllers\PaymentController@approval')->name('approval');
Route::get('/payments/cancelled', 'App\Http\Controllers\PaymentController@cancelled')->name('cancelled');
Route::get('/payments/success', function(){
    return view('payment.success');
})->name('payment.success');



/*for test purpose*/
Route::get('/php',function() {
    phpinfo();
});
Route::get('/clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    return "Cleared!";
});
Route::get('/optimize-clear',function(){
    Artisan ::call('optimize:clear');
    return "Optimize Cleared!";
});
Route::get('get-envs',function() {

    dump(env('STRIPE_BASE_URI'));
    dump(env('STRIPE_KEY'));
    dump(env('STRIPE_SECRET'));
    dump(env('FIREBASE_BUSINESS_AUTH_KEY_ID'));
    dump(env('FIREBASE_CUSTOMER_AUTH_KEY_ID'));

});

Route::get('/migrate', function () {
    $exitCode = Artisan::call('migrate:fresh');
    return "Migration completed with exit code: $exitCode";
});
Route::get('/seed', function () {
    $exitCode = Artisan::call('db:seed');
    return "Seeding completed with exit code: $exitCode";
});

Route::get('/get-env',function() {
    dd($_ENV);
});

Route::get('send-email',function() {
    $business = Business::find(15);
     Mail::to($business->email)->send(new SendTrialExpirationMail($business));
     dd('sent');
});

Route::get('success',function() {
    dd(request()->all());
});




