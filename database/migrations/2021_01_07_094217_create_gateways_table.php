<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGatewayDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gateway_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('node_data_id');
            $table->unsignedBigInteger('gateway_id');
            $table->float('snr');
            $table->integer('rssi');
            $table->foreign('node_data_id')->references('id')->on('node_data')->onDelete('cascade');
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
        Schema::dropIfExists('gateway_data');
    }
}
