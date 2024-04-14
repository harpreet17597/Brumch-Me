<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessAvailabilityAndTimeSlotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_availability_and_time_slots', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('business_id')->unsigned()->index();
            $table->dateTime('availability_date');
            $table->string('time_slot_from');
            $table->string('time_slot_to');
            $table->foreign('business_id')->references('id')->on('users');

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
        Schema::dropIfExists('business_availability_and_time_slots');
    }
}
