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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained()->cascadeOnDelete();
            $table->integer('sequence');
            $table->date('date');
            $table->string('category_group');
            $table->string('weight_group');
            $table->integer('mat');
            $table->integer('section');
            $table->string('contest_system');
            $table->unsignedTinyInteger('chart_size');
            $table->time('duration');
            $table->unsignedSmallInteger('status')->default(0);          
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
