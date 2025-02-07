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
        Schema::create('competition_referee', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id');
            $table->foreignId('referee_id');
            $table->tinyInteger('serial_number')->nullable();
            $table->tinyInteger('mat_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_referee');
    }
};
