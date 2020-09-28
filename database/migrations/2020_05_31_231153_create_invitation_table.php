<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvitationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invitation', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('invitation_Id')->nullable();
            $table->integer('account_Id')->nullable();
            $table->integer('sender_Id')->nullable();
            $table->integer('email')->nullable();
            $table->string('message')->nullable();
            $table->integer('role_Id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invitation');
    }
}
