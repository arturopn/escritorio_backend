<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->bigIncrements('couponId');
            $table->date('date');
            $table->date('expDate');
            $table->string('couponName');
            $table->string('couponCode');
            $table->string('couponType');
            $table->string('discount');
            $table->string('photo')->nullable();
            $table->string('description');
            $table->integer('user_id');
            $table->foreign('user_id')->references('userId')->on('users');
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
        Schema::dropIfExists('coupons');
    }
}
