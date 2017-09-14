<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {

            $table->increments('id');
            $table->string('username', 20)->nullable()->unique();
            $table->string('password', 60)->nullable();
            $table->enum('userRole', ['Administrator', 'Manager', 'Accountant', 'Nurse', 'Receptionist', 'Pharmacist', 'Doctor']);
            $table->boolean('disabled')->default(false);
            $table->integer('employee_id')->nullable()->unsigned()->index();
            $table->integer('doctor_id')->nullable()->unsigned()->index();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::table('users', function ($table) {

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('doctor_id')->references('id')->on('doctors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
