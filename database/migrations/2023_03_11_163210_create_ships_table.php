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
        Schema::create('ships', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default("");
            $table->string('type')->default("");
            $table->integer('number_of_canons')->default(0);
            $table->double('damage_canons')->default(0);
            $table->double('strong_crew')->default(0);
            $table->double('armament')->default(0);
            $table->integer('hp')->default(0);
            $table->integer('price')->default(0);
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
        Schema::dropIfExists('ships');
    }
};