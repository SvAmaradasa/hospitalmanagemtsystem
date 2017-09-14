<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class PrescriptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('appointment_id')->unsigned()->index();
            $table->integer('doctor_id')->unsigned()->index();
            $table->integer('patient_id')->unsigned()->index();
            $table->text('symptoms');
            $table->text('diagnosis');
            $table->boolean('issued');
            $table->boolean('paid');
            $table->timestamps();
        });

        Schema::table('prescriptions', function ($table) {
            $table->foreign('appointment_id')->references('id')->on('appointments');
            $table->foreign('doctor_id')->references('id')->on('doctors');
            $table->foreign('patient_id')->references('id')->on('patients');
            $table->unique(['appointment_id', 'doctor_id', 'patient_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('prescriptions');
    }
}
