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
        Schema::create('competitions', function (Blueprint $table) {
            $table->id();

            $table->date('date_start');
            $table->date('date_end');
            $table->string('country');
            $table->string('name');
            $table->string('name_secondary')->nullable();
            $table->string('scale')->nullable();
            $table->json('days')->nullable();
            $table->string('remark')->nullable();
            $table->unsignedTinyInteger('mat_number')->default(1);
            $table->unsignedTinyInteger('section_number')->default(1);
            $table->string('token');
            $table->unsignedSmallInteger('status');
            $table->char('system', 1); //Quarter | Full | KO
            $table->tinyInteger('seeding');
            $table->string('small_system');
            $table->char('type'); //individual | teams
            $table->tinyInteger('gender'); //2=male & female | 1=male | 0=female
            // $table->foreignId('game_category_id'); //age group in IJF
            $table->boolean('is_cancelled');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competitions');
    }
};
