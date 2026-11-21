<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->foreignId('organization_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('period_id')->nullable()->constrained('management_periods')->nullOnDelete();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('total_budget', 15, 2)->default(0);
            $table->decimal('requested_campus_fund', 15, 2)->default(0);
            $table->decimal('approved_campus_fund', 15, 2)->nullable();
            $table->enum('status', ['Draft', 'Diajuan', 'Disetujui', 'Ditolak', 'Selesai'])->default('Draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
