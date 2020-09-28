<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQRCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::dropIfExists('qr_codes');

        Schema::create('qr_codes', function (Blueprint $table) {
            $table->bigIncrements('qrId');
            $table->string('image')->nullable();
            $table->integer('establishmentId')->unsigned()->nullable();
            $table->foreign('establishmentId')->references('establishmentId')->on('establishments');
            $table->boolean('inUse');
            $table->string('door');
            $table->string('qrcode');
            $table->string('location')->nullable();
            $table->string('qrToken')->nullable();
            $table->string('type');
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
        Schema::dropIfExists('qr_codes');
    }
}
