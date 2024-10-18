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
        Schema::create('competition_type', function (Blueprint $table) {
            $table->id();

            $table->foreignId('competition_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('name_secondary');
            $table->char('code', 5);
            $table->boolean('awarding_methods');
            $table->string('language')->nullable();
            $table->boolean('is_language_secondary_enabled');
            $table->string('language_secondary')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_type');
    }
};
