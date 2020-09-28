<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('histories', function (Blueprint $table) {
            $table->bigIncrements('historyId');
            $table->integer('userId')->unsigned();
            $table->foreign('userId')->references('userId')->on('users');
            $table->integer('paymentId')->unsigned()->nullable();
            $table->foreign('paymentId')->references('paymentId')->on('payments');
            $table->integer('establishmentId')->unsigned();
            $table->foreign('establishmentId')->references('establishmentId')->on('establishments');
            $table->dateTimeTz('entryTime');
            $table->dateTimeTz('exitTime')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('histories');
    }
}
