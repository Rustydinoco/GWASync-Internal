<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sponsorship_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->nullable()->constrained('events')->nullOnDelete();
            $table->string('name', 100);
            $table->decimal('min_amount', 15, 2);
            $table->text('benefits')->nullable();
            $table->integer('quota')->nullable();
            $table->integer('seq_no')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sponsorship_packages');
    }
};
