<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurant_menus', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('restaurant_id')->unsigned()->index();
            $table->string('restaurant_menu_name');
            $table->float('restaurant_menu_price');
            $table->string('restaurant_menu_quantity');
            $table->text('restaurant_menu_description')->nullable();
            $table->text('restaurant_menu_image')->nullable();
            $table->foreign('restaurant_id')->references('id')->on('restaurants');
            $table->timestamps();

            $table->index(['restaurant_menu_name','created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('restaurant_menus');
    }
}
