<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('paymentId');
            $table->integer('userId')->unsigned();
            $table->foreign('userId')->references('userId')->on('users');
            $table->float('amount');
            $table->string('reference');
            $table->integer('paymentMethod');
            $table->string('agreement');
            $table->date('date');
            $table->integer('plateId')->unsigned()->nullable();
            $table->foreign('plateId')->references('plateId')->on('plates');
            $table->time('time')->nullable();
            $table->integer('couponId')->unsigned();
            $table->foreign('couponId')->references('couponId')->on('coupons');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
