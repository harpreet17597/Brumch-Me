<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantTableBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurant_table_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_number')->nullable();
            $table->bigInteger('customer_id')->unsigned()->index();
            $table->bigInteger('business_id')->unsigned()->index();
            $table->bigInteger('restaurant_id')->unsigned()->index();
            $table->dateTime('booking_from_date_time');
            $table->dateTime('booking_to_date_time');
            $table->integer('number_of_persons');
            $table->enum('status',['pending','confirmed','cancelled'])->default('pending');
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('users');
            $table->foreign('business_id')->references('id')->on('users');
            $table->foreign('restaurant_id')->references('id')->on('restaurants');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('restaurant_table_bookings');
    }
}
