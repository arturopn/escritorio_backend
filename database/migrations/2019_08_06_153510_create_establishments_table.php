<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEstablishmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('establishments', function (Blueprint $table) {
            $table->bigIncrements('establishmentId');
            $table->string('location')->nullable();
            $table->string('address');
            $table->string('name');
            $table->string('logo')->nullable();
            $table->string('capacity')->nullable();
            $table->string('rfc')->nullable();
            $table->string('openpayid')->nullable();
            $table->integer('ownerId')->nullable();
            $table->string('fee_type')->nullable();
            $table->float('amount')->nullable();
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
       Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('establishments');
    }
}
