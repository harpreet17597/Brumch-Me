<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
   
            $table->id();
            $table->string('name')->nullable();
            $table->string('display_name')->nullable();
            $table->date('dob')->nullable();
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->text('email_verify_token')->nullable();
            $table->string('username')->unique()->nullable();
            $table->string('password')->nullable();
            $table->text('phone_country')->nullable();
            $table->text('country_code')->nullable();
            $table->string('phone')->nullable()->unique();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('timezone')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('profile_background')->nullable();
            $table->text('about')->nullable();
            /*Account*/
            $table->enum('user_type', ['customer', 'business'])->default('customer');
            $table->smallInteger('is_verified')->default(0)->description(' 0=>No 1 =>yes');
            $table->timestamp('verified_at')->nullable();
            $table->smallInteger('is_suspended')->default(0)->description(' 0=>No 1 =>yes');
            $table->timestamp('suspended_at')->nullable();
            /*For Business Account*/
            $table->smallInteger('account_status')->default(1);
            /*Address*/
            $table->integer('country_id')->nullable();
            $table->integer('state_id')->nullable();
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('suburb')->nullable();
            $table->string('city')->nullable();
            $table->string('post_code')->nullable();
            $table->string('current_location')->nullable();
            $table->string('street_address')->nullable();
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            /*Business*/
            $table->string('restaurant_opening_time')->nullable();
            $table->string('restaurant_closing_time')->nullable();
            $table->bigInteger('restaurant_max_table')->nullable();
            $table->integer('terms_condition')->default(1);
            $table->text('app_id')->nullable();
            $table->string('device_os')->nullable();
            $table->text('apple_id')->nullable();
            $table->text('fcm_token')->nullable();
            $table->timestamp('last_seen')->nullable();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['name','email','created_at']);
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
