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
        Schema::create('fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('node_id');
            $table->Integer('position');
            $table->string('name');
            $table->boolean('visible');
            $table->string('unit');
            $table->string('primarycolor');
            $table->string('secondarycolor');
            $table->boolean('isdashed');
            $table->boolean('isfilled');
            $table->foreign('node_id')->references('id')->on('nodes')->onDelete('cascade');
            $table->boolean('exceeded');
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
