<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('schedule_id')->nullable()->constrained('activity_schedules')->nullOnDelete();
            $table->string('name', 150);
            $table->datetime('open_at');
            $table->datetime('close_at');
            $table->integer('late_tolerance_minute')->default(10);
            $table->string('location', 255)->nullable();
            $table->string('qr_token', 100)->unique()->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->integer('radius_meter')->nullable();
            $table->enum('status', ['Aktif', 'Ditutup', 'Dibatalkan'])->default('Aktif');
            $table->string('cancel_reason', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_sessions');
    }
};
