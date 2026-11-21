<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('management_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['Aktif', 'Nonaktif', 'Tutup'])->default('Aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('management_periods');
    }
};
