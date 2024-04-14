<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserFeaturedSubscriptionStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_featured_subscription_statuses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('status');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('start_date_unix');
            $table->string('end_date_unix');
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
        Schema::dropIfExists('user_featured_subscription_statuses');
    }
}
