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
        Schema::create('borrowings', function (Blueprint $table) {
            $table->id();
            
            // 1. Buat kolom sekaligus relasinya secara otomatis
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('inventory_id')->constrained('inventories')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();

            $table->datetime('start_date');
            $table->datetime('end_date');
            
            // 2. Gunakan enum status transaksi (sesuai form Vue)
            $table->enum('status', ['Pending', 'Disetujui', 'Ditolak', 'Dikembalikan'])->default('Pending');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrowings');
    }
};
