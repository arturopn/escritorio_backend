<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rates', function (Blueprint $table) {
            $table->bigIncrements('rateId');
            $table->integer('establishmentId');
            $table->integer('tolerance')->nullable();
            $table->float('charge_1');
            $table->boolean('is_double')->nullable();
            $table->float('charge_2')->nullable();
            $table->float('subsequent')->nullable();
            $table->datetime('from')->nullable();
            $table->datetime('to')->nullable();
            $table->boolean('one_time_payment')->nullable();
            $table->integer('userId')->unsigned();
            $table->foreign('userId')->references('userId')->on('users');
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
        Schema::dropIfExists('rates');
    }
}
