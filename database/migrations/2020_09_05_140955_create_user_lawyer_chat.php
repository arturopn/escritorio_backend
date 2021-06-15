<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserLawyerChat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_lawyer_chat', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('chatid')->nullable();
            $table->string('user_id')->nullable();
            $table->string('lawyer_id')->nullable();
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
        Schema::dropIfExists('user_lawyer_chat');
    }
}
