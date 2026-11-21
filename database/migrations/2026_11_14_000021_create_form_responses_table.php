<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('forms')->cascadeOnDelete();
            $table->foreignId('member_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('respondent_name', 150)->nullable();
            $table->string('respondent_email', 150)->nullable();
            $table->json('answers');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_responses');
    }
};
