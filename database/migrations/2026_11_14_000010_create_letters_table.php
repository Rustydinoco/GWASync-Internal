<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('letters', function (Blueprint $table) {
            $table->id();
            $table->string('letter_number', 50)->unique();
            $table->string('title', 200);
            $table->text('content')->nullable();
            $table->string('recipient', 150)->nullable();
            $table->string('sender', 150)->nullable();
            $table->date('letter_date');
            $table->string('category', 100)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['Draft', 'Diajuan', 'Disetujui', 'Ditolak', 'Dikirim'])->default('Draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letters');
    }
};
