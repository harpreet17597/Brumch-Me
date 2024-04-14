<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurant_tags', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('restaurant_id')->unsigned()->index();
            $table->bigInteger('tag_id')->unsigned()->index();
            $table->foreign('restaurant_id')->references('id')->on('restaurants');
            $table->foreign('tag_id')->references('id')->on('tags');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('restaurant_tags');
    }
}
