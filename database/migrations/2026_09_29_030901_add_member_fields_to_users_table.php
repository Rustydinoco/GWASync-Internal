<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
    Schema::table('users', function (Blueprint $table) {
        if (!Schema::hasColumn('users', 'nim')) {
            $table->string('nim')->unique()->nullable()->after('email');
        }
        
        if (!Schema::hasColumn('users', 'phone')) {
            $table->string('phone')->nullable()->after('nim');
        }
        
        if (!Schema::hasColumn('users', 'division')) {
            $table->enum('division', ['Humas', 'Latbang', 'Sarpras', 'Medkraf', 'BPH', 'Usaha & Kemitraan', 'DPA'])->nullable();
        }
        
        if (!Schema::hasColumn('users', 'status')) {
            $table->enum('status', ['Aktif', 'Pasif', 'Alumni'])->default('Aktif');
        }
    });
}

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nim', 'phone', 'division', 'status']);
        });
    }
};