<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdsReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ads_reports', function (Blueprint $table) {
            $table->bigIncrements('adReportId');
            $table->integer('userId')->unsigned();
            $table->foreign('userId')->references('userId')->on('users');
            $table->integer('view');
            $table->integer('click')->nullable();
            $table->date('date');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ads_reports');
    }
}
