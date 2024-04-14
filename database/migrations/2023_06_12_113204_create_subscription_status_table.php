<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscription_status', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unsigned()->index();
            $table->string('status');
            $table->integer('current_plan_id');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->integer('start_date_unix');
            $table->integer('end_date_unix');
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
        Schema::dropIfExists('subscription_status');
    }
}
