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
        Schema::create('athletes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained()->cascadeOnDelete();
            $table->string('name_zh');
            $table->string('name_pt')->nullable();
            $table->string('name_display');
            $table->string('gender');
            $table->integer('team_id');
            $table->integer('seed');
            $table->integer('seat');
            $table->double('weight')->nullable();
            // unique athlete identifier, will be used later
            $table->string('uai')->nullable();            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('athletes');
    }
};
