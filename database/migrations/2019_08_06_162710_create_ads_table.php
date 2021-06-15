<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ads', function (Blueprint $table) {
            $table->bigIncrements('adId');
            $table->string('media');
            $table->string('name');
            $table->string('description');
            $table->integer('establishmentId')->unsigned();
            $table->foreign('establishmentId')->references('establishmentId')->on('establishments');
            $table->integer('minAge')->nullable();
            $table->integer('maxAge')->nullable();
            $table->integer('gender')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ads');
    }
}
