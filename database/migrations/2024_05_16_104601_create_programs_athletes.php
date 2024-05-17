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
        Schema::create('programs_athletes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->foreignId('athlete_id')->constrained()->cascadeOnDelete();
            $table->foreignId('competition_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('seed')->nullable();
            $table->unsignedTinyInteger('seat')->default(0);
            $table->double('weight')->nullable();
            $table->boolean('is_weight_passed')->nullable();
            $table->tinyInteger('rank')->default(0);
            $table->tinyInteger('score')->default(0);
            $table->boolean('confirm')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs_athletes');
    }
};
