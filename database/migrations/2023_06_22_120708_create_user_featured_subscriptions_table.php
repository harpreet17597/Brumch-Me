<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserFeaturedSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_featured_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('payment_id');
            $table->string('amount');
            $table->string('currency');
            $table->string('charge_id');
            $table->string('payment_intent');
            $table->string('payment_method');
            $table->string('balance_transaction');
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
        Schema::dropIfExists('user_featured_subscriptions');
    }
}
