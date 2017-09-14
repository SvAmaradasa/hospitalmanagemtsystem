<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AppointmentDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointment_details', function (Blueprint $table) {

            $table->integer('patient_id')->unsigned()->index();
            $table->bigInteger('appointment_id')->unsigned()->index();

        });

        Schema::table('appointment_details', function ($table) {

            $table->foreign('patient_id')->references('id')->on('patients');
            $table->foreign('appointment_id')->references('id')->on('appointments');
            $table->primary(['patient_id', 'appointment_id']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('appointment_details');
    }
}
