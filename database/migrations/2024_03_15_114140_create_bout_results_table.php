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
        Schema::create('bout_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bout_id')->constrained()->cascadeOnDelete();

            $table->tinyInteger('status')->default(0);

            $table->unsignedTinyInteger('w_ippon')->default(0);
            $table->unsignedTinyInteger('w_wazari')->default(0);
            $table->unsignedTinyInteger('w_shido')->default(0);

            $table->unsignedTinyInteger('b_ippon')->default(0);
            $table->unsignedTinyInteger('b_wazari')->default(0);
            $table->unsignedTinyInteger('b_shido')->default(0);

            $table->unsignedTinyInteger('w_score')->default(0);
            $table->unsignedTinyInteger('b_score')->default(0);

            $table->time('time')->default(0);
            $table->string('device_uuid', '64')->nullable();
            $table->json('actions')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bout_results');
    }
};
