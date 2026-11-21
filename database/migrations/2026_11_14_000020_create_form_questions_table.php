<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('forms')->cascadeOnDelete();
            $table->string('question', 500);
            $table->enum('question_type', ['Teks', 'Pilihan', 'Ceklist', 'File'])->default('Teks');
            $table->text('options')->nullable();
            $table->enum('is_required', [0, 1])->default(1);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_questions');
    }
};
