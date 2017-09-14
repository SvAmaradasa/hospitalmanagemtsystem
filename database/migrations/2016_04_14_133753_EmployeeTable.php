<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class EmployeeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->increments('id');
            $table->char('displayId', 7);
            $table->enum('title', ['Mr', 'Mrs', 'Ms', 'Miss', 'Rev', 'Dr']);
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
        Schema::drop('employees');
    }
}
