<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('events')->cascadeOnDelete();
            $table->integer('day_of_week');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('location', 255)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->integer('radius_meter')->nullable();
            $table->integer('late_tolerance_minute')->default(10);
            $table->date('valid_from');
            $table->date('valid_until')->nullable();
            $table->enum('is_active', [0, 1])->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_schedules');
    }
};
