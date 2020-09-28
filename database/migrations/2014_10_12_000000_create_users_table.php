<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Models\User;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('userId');
            $table->string('name')->nullable();;
            $table->string('lastName')->nullable();;
            $table->string('email')->unique()->nullable();
            $table->string('password')->nullable();;
            $table->string('cellphone')->unique()->nullable();
            $table->string('facebookToken')->nullable();
            $table->string('googleToken')->nullable();
            $table->string('photo')->nullable();
            $table->integer('gender')->nullable();
            $table->integer('age')->nullable();
            $table->string('status');
            $table->boolean('isLogged');
            $table->string('verified')->default(User::UNVERIFIED_USER);
            $table->string('verification_token')->nullable();
            $table->string('firebase_registration_token')->nullable();
            $table->string('open_pay_token')->nullable();
            $table->string('paypal_token')->nullable();
            $table->dateTime('last_entry')->nullable();
            $table->string('coupon_code', 10)->unique();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
