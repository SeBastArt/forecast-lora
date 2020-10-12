<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateForecastItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forecast_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('forecast_id')->unsigned();
            $table->foreign('forecast_id')->references('id')->on('forecasts')->onDelete('cascade');
            $table->timestamp('valid_from')->nullable()->default(null);
            $table->float('temp');
            $table->float('humidity');
            $table->unsignedBigInteger('weather_id');
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
        Schema::dropIfExists('forecast_items');
    }
}
