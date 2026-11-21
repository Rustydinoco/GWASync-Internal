<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('account_id')->constrained('financial_accounts');
            $table->foreignId('category_id')->constrained('financial_categories');
            $table->foreignId('period_id')->nullable()->constrained('management_periods')->nullOnDelete();
            $table->foreignId('budget_item_id')->nullable()->constrained('financial_categories')->nullOnDelete();
            $table->foreignId('funding_request_id')->nullable()->constrained('funding_requests')->nullOnDelete();
            $table->foreignId('activity_id')->nullable()->constrained('events')->nullOnDelete();
            $table->foreignId('program_id')->nullable()->constrained('programs')->nullOnDelete();
            $table->string('transaction_number', 50)->unique();
            $table->enum('transaction_type', ['Pemasukan', 'Pengeluaran'])->default('Pemasukan');
            $table->decimal('amount', 15, 2);
            $table->date('transaction_date');
            $table->string('description', 255)->nullable();
            $table->enum('status', ['Pending', 'Disetujui', 'Ditolak'])->default('Pending');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->datetime('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
    }
};
