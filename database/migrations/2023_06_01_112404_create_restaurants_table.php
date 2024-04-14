<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->index();
            $table->string('restaurant_name');
            $table->text('restaurant_description')->nullable();
            $table->text('restaurant_address')->nullable();
            $table->string('restaurant_opening_time')->nullable();
            $table->string('restaurant_closing_time')->nullable();
            $table->text('restaurant_latitude')->nullable();
            $table->text('restaurant_longitude')->nullable();
            $table->float('restaurant_rating')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();

            $table->index(['restaurant_name','created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('restaurants');
    }
}
