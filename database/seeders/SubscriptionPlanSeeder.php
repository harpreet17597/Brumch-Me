<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      Schema::disableForeignKeyConstraints();
      DB::table('subscription_plans')->truncate();
      Schema::enableForeignKeyConstraints();

        $records = DB::table('subscription_plans')->count();
        if(!$records) {

            $plans = [

              [
                'name'              => 'Weekly',
                'product_id'        => 'com.brunch.weekly_subscription',
                'description'       => 'basic', 
                'title'             => '$99/Week',
                'price'             =>  99,
                'sku'               => 'weekly_subscriptions',
                'interval'          => 'weekly',
                'interval_duration' => 1,
                'features'          => json_encode([]),
                'status'            => 1
              ],
              [
                'name'              => 'Monthly',
                'product_id'        => 'com.brunch.monthly_subscriptions',
                'description'       => 'medium', 
                'title'             => '$350/Month',
                'price'             =>  350,
                'sku'               => 'monthly_subscriptions',
                'interval'          => 'monthly',
                'interval_duration' => 1,
                'features'          => json_encode([]),
                'status'            => 1
              ],
              [
                'name'              => 'Three Months',
                'product_id'        => 'com.brunch.three_month_subscriptions',
                'description'       => 'premium', 
                'title'             => '$999/3-Months',
                'price'             =>  999,
                'sku'               => 'three_month_subscriptions',
                'interval'          => 'monthly',
                'interval_duration' => 3,
                'features'          => json_encode([]),
                'status'            => 1
              ]
            ];

            DB::table('subscription_plans')->insert($plans);
        }
    }
}
