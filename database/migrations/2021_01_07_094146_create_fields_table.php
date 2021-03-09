<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //todo: default values for every Model
        Schema::create('fields', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('visible');
            $table->string('unit');
            $table->string('primary_color');
            $table->string('secondary_color');
            $table->boolean('is_dashed');
            $table->boolean('is_filled');
            $table->float('upper_limit')->default(0.0);
            $table->boolean('check_upper_limit')->default(false);
            $table->float('lower_limit')->default(0.0);
            $table->boolean('check_lower_limit')->default(false);
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
        Schema::dropIfExists('fields');
    }
}
