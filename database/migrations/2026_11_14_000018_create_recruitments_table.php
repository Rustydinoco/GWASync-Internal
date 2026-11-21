<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recruitments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->unique()->constrained('events')->cascadeOnDelete();
            $table->datetime('open_at');
            $table->datetime('close_at');
            $table->integer('quota')->nullable();
            $table->foreignId('form_id')->nullable()->constrained('forms')->nullOnDelete();
            $table->enum('status', ['Buka', 'Tutup', 'Selesai'])->default('Buka');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recruitments');
    }
};
