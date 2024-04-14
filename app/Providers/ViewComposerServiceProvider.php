<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\Admin;
use App\Models\Business;
use App\Models\Customer;
use App\Models\RestaurantTableBooking;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {   
        $users_count = null;
        $bookings_count = null;
        $latest_customer_users = null;
        $latest_business_users = null;

        try {

            $users_role_group = User::groupBy('user_type')->select('user_type', DB::raw('count(*) as count'))->get();
            if($users_role_group) {
                foreach($users_role_group as $record) {
                    $users_count[$record->user_type] = $record->count;
                }
            }
            $users_count['admin'] = Admin::count();
    
            $bookings_status_group = RestaurantTableBooking::groupBy('status')->select('status',DB::raw('count(*) as count'))->get();
            if($bookings_status_group) {
                foreach($bookings_status_group as $record) {
                    $bookings_count[$record->status] = $record->count;
                }
            }
    
            $latest_customer_users = Customer::latest()->take(5)->get();
            $latest_business_users = Business::latest()->take(5)->get();
        }
        catch (\Exception $e) {

        }


        View::composer('*', function ($view) use($users_count,$bookings_count,$latest_customer_users,$latest_business_users) {
            $view->with([
                           'users_count'           => $users_count,
                           'bookings_count'        => $bookings_count,
                           'latest_customer_users' => $latest_customer_users,
                           'latest_business_users' => $latest_business_users
                        ]);
        });
    }
}
