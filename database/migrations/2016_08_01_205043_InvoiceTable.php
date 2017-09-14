<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class InvoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->bigInteger('appointment_id')->unsigned()->index();
            $table->bigInteger('prescription_id')->nullable()->unsigned()->index();
            $table->integer('drug_id')->nullable()->unsigned()->index();
            $table->integer('fee_id')->nullable()->unsigned()->index();
            $table->decimal('feeValue', 7, 2);
            $table->smallInteger('qty');
            $table->timestamps();

        });

        Schema::table('invoices', function ($table) {
            $table->foreign('appointment_id')->references('id')->on('appointments');
            $table->foreign('fee_id')->references('id')->on('fees');
            $table->foreign('drug_id')->references('id')->on('drugs');
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
        Schema::drop('invoices');
    }
}
