<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class PatientTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->increments('id');
            $table->char('displayId', 11);
            $table->enum('title', ['Mr', 'Mrs', 'Ms', 'Miss', 'Rev', 'Dr']);
            $table->string('firstName', 30);
            $table->string('middleName', 30)->nullable();
            $table->string('lastName', 30);
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->date('birthday')->nullable();
            $table->enum('maritalStatus', ['Single', 'Married', 'Divorced', 'Widowed'])->nullable();
            $table->string('address', 50)->nullable();
            $table->string('city', 20)->nullable();
            $table->string('nic', 12)->nullable();
            $table->char('telephoneNo', 10)->nullable();
            $table->char('mobileNo', 10)->nullable();
            $table->string('email', 40)->nullable();
            $table->string('emergencyContact', 50)->nullable();
            $table->char('emergencyContactPhone', 10)->nullable();
            $table->string('emergencyContactRelation', 30)->nullable();
            $table->string('employer', 50)->nullable();
            $table->string('occupation', 30)->nullable();
            $table->char('employerPhone', 10)->nullable();
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
        Schema::drop('patients');
    }
}
