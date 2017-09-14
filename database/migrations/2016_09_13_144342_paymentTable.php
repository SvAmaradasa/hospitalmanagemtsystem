<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class PaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->bigInteger('appointment_id')->unsigned()->index();
            $table->bigInteger('prescription_id')->nullable()->unsigned()->index();
            $table->decimal('paidAmount', 14, 2);
            $table->enum('paymentMethod', ['Cash', 'Visa', 'Master', 'Amex']);
            $table->char('cardNo', 16)->nullable();
            $table->char('reference', 6)->nullable();
            $table->integer('employee_id')->unsigned()->index();
            $table->timestamps();
        });

        Schema::table('payments', function ($table) {

            $table->foreign('appointment_id')->references('id')->on('appointments');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('prescription_id')->references('id')->on('prescriptions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('payments');
    }
}
