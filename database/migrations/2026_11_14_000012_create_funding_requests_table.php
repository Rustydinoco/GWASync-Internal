<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('funding_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('program_id')->nullable()->constrained('programs')->nullOnDelete();
            $table->foreignId('activity_id')->nullable()->constrained('events')->nullOnDelete();
            $table->string('request_number', 50)->unique();
            $table->string('title', 200);
            $table->text('reason');
            $table->decimal('total_estimate', 15, 2);
            $table->decimal('total_actual', 15, 2)->default(0);
            $table->date('needed_by_date')->nullable();
            $table->enum('payment_method', ['Tunai', 'Transfer', 'Pengajuan Dana'])->default('Tunai');
            $table->foreignId('funding_request_id')->nullable()->constrained('funding_requests')->nullOnDelete();
            $table->enum('status', ['Draft', 'Diajuan', 'Disetujui', 'Ditolak', 'Selesai'])->default('Draft');
            $table->string('reject_reason', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funding_requests');
    }
};
