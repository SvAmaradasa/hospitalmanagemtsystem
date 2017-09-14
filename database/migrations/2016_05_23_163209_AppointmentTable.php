<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AppointmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->char('displayId', 22);
            $table->smallInteger('appointmentNo');
            $table->date('date');
            $table->enum('appointmentStatus', ['New', 'Paid', 'In Progress', 'Close', 'Canceled', 'Refunded']);
            $table->integer('doctor_id')->unsigned()->index()->nullable();
            $table->enum('appointmentType', ['OPD', 'Channelling', 'Lab', 'Scan', 'X-Ray']);
            $table->integer('company_id')->unsigned()->index()->nullable();
            $table->integer('employee_id')->unsigned()->index();
            $table->integer('scan_id')->unsigned()->index()->nullable();
            $table->integer('xray_id')->unsigned()->index()->nullable();
            $table->integer('lab_id')->unsigned()->index()->nullable();
            $table->string('report', 100)->nullable();
            $table->timestamps();

        });

        Schema::table('appointments', function ($table) {

            $table->foreign('doctor_id')->references('id')->on('doctors');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('scan_id')->references('id')->on('scans');
            $table->foreign('xray_id')->references('id')->on('xrays');
            $table->foreign('lab_id')->references('id')->on('labs');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('appointments');
    }
}
