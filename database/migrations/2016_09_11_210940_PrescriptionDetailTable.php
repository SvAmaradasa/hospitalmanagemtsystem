<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class PrescriptionDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prescription_details', function (Blueprint $table) {
            $table->integer('drug_id')->unsigned()->index();
            $table->bigInteger('prescription_id')->unsigned()->index();
            $table->smallInteger('days');
            $table->string('schedule', 50);
            $table->string('note', 250)->nullable();
        });

        Schema::table('prescription_details', function ($table) {
            $table->foreign('drug_id')->references('id')->on('drugs');
            $table->foreign('prescription_id')->references('id')->on('prescriptions');
            $table->primary(['drug_id', 'prescription_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('prescription_details');
    }
}
