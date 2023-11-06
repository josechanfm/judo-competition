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
            $table->foreignId('competition_category_id');
            $table->date('date_start');
            $table->date('date_end');
            $table->string('country');
            $table->string('name_en');
            $table->string('name_fn')->nullable();
            $table->string('scale')->nullable();
            $table->json('days')->nullable();
            $table->string('remark')->nullable();
            $table->unsignedTinyInteger('mat_number')->default(1);
            $table->unsignedTinyInteger('section_number')->default(1);
            $table->string('token');
            $table->unsignedSmallInteger('status');
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
