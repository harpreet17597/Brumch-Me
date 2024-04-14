<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->index();
            $table->bigInteger('plan_id')->unsigned()->index();
            $table->string('product_id')->nullable();
            $table->string('transactionId')->nullable();
            $table->dateTime('transactionDate')->nullable();
            $table->binary('transactionReceipt')->nullable();
            $table->string('purchaseToken')->nullable();
            $table->double('amount')->nullable();
            $table->string('currency')->nullable();
            $table->string('status')->nullable();
            $table->datetime('start_date')->nullable();
            $table->datetime('end_date')->nullable();
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
        Schema::dropIfExists('user_subscriptions');
    }
}
