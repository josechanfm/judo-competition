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
        Schema::create('bouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('sequence')->nullable();
            $table->unsignedInteger('in_program_sequence')->nullable();
            $table->unsignedInteger('queue')->nullable();

            // copy from program
            $table->date('date')->nullable();
            $table->unsignedSmallInteger('mat')->nullable();
            $table->unsignedSmallInteger('section')->nullable();
            $table->string('contest_system')->nullable();
            $table->smallInteger('round')->nullable();  
            $table->smallInteger('turn')->nullable();

            // player in this bout
            $table->bigInteger('white')->nullable();
            $table->bigInteger('blue')->nullable();

            // connection to other bouts
            $table->bigInteger('white_rise_from')->default(0);
            $table->bigInteger('blue_rise_from')->default(0);
            $table->bigInteger('winner_rise_to')->default(0);
            $table->foreignId('loser_rise_to')->default(0);

            $table->bigInteger('winner')->default(0);
            $table->foreignId('white_score')->default(0);
            $table->foreignId('blue_score')->default(0);
            $table->integer('duration')->default(0);

            $table->tinyInteger('status')->default(0);            
            $table->timestamps();

            $table->unique(['program_id', 'in_program_sequence']);

        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bouts');
    }
};
