<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class FeeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fees', function (Blueprint $table) {

            $table->increments('id');
            $table->enum('feeType', ['OPD', 'Channelling', 'Lab', 'Scan', 'X-Ray']);
            $table->string('description', 100);
            $table->decimal('fee', 7, 2)->nullable();
            $table->boolean('isVariable');
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
        Schema::drop('fees');
    }
}
