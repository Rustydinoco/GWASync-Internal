<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('letter_dispositions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('letter_id')->constrained('letters')->cascadeOnDelete();
            $table->foreignId('from_user_id')->constrained('users');
            $table->foreignId('to_member_id')->constrained('users');
            $table->string('instruction', 500);
            $table->date('due_date')->nullable();
            $table->enum('status', ['Belum', 'Selesai'])->default('Belum');
            $table->datetime('done_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letter_dispositions');
    }
};
