<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ships_fleets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fleet_id')->references('id')->on('fleets')->onDelete('cascade');
            $table->foreignId('ship_id')->references('id')->on('ships')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ships_fleets');
    }
};
