<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        
        //$this->call(UserTableSeeder::class);
       //$this->call(TagTableSeeder::class);
       $this->call(SubscriptionPlanSeeder::class);
      // $this->call(PaymentPlatformsTableSeeder::class);
       $this->call(AdminTableSeeder::class);
      // $this->call(CurrenciesTableSeeder::class);
       
    }
}
