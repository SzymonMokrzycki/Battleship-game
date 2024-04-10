<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('battles', function (Blueprint $table) {
            $table->id();
            $table->integer('number_lost_ships')->default(0);
            $table->string('result')->default("");
            $table->double('experience')->default(0);
            $table->double('battle_points')->default(0);
            $table->foreignId('user_id')->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('fleet_id')->nullable()->references('id')->on('fleets')->onDelete('cascade');
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
        Schema::dropIfExists('battles');
    }
};