<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class DoctorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doctors', function (Blueprint $table) {

            $table->increments('id');
            $table->char('displayId', 11);
            $table->enum('title', ['Dr']);
            $table->string('firstName', 30);
            $table->string('middleName', 30);
            $table->string('lastName', 30);
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->enum('maritalStatus', ['Single', 'Married', 'Divorced', 'Widowed']);
            $table->string('address', 50);
            $table->string('city', 20);
            $table->date('birthday');
            $table->string('nic', 12);
            $table->char('telephoneNo', 10)->nullable();
            $table->char('mobileNo', 10)->nullable();
            $table->string('email', 40)->unique();
            $table->string('degree', 50)->nullable();
            $table->enum('doctorType', ['OPD', 'Channelling']);
            $table->integer('doctor_specialty_id')->unsigned()->index()->nullable();
            $table->string('hospital', 50)->nullable();
            $table->decimal('fees', 6, 2);
            $table->timestamps();
        });

        Schema::table('doctors', function ($table) {
            $table->foreign('doctor_specialty_id')->references('id')->on('doctor_specialties');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('doctors');
    }
}
