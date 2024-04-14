<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDressCodeFilesToRestaurantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('restaurants', function (Blueprint $table) {
            
            $table->enum('has_dress_code',['no','yes'])->default('no')->after('restaurant_rating');
            $table->text('dress_code')->nullable()->after('has_dress_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn('has_dress_code');
            $table->dropColumn('dress_code');
        });
    }
}
