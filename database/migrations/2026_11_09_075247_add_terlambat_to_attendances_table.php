<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            // PostgreSQL: drop check constraint, alter column, add new check
            DB::statement('ALTER TABLE attendances DROP CONSTRAINT IF EXISTS attendances_status_check');
            DB::statement("ALTER TABLE attendances ALTER COLUMN status TYPE varchar(255)");
            DB::statement("ALTER TABLE attendances ALTER COLUMN status SET DEFAULT 'Alpha'");
            DB::statement("ALTER TABLE attendances ALTER COLUMN status SET NOT NULL");
            DB::statement("ALTER TABLE attendances ADD CONSTRAINT attendances_status_check CHECK (status IN ('Hadir', 'Izin', 'Sakit', 'Alpha', 'Terlambat'))");
            DB::statement("ALTER TABLE attendances ADD COLUMN IF NOT EXISTS keterangan text NULL");
        } elseif (DB::getDriverName() === 'sqlite') {
            Schema::create('attendances_new', function (Blueprint $table) {
                $table->id();
                $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->dateTime('scan_time')->nullable();
                $table->enum('status', ['Hadir', 'Izin', 'Sakit', 'Alpha', 'Terlambat'])->default('Alpha');
                $table->text('keterangan')->nullable();
                $table->timestamps();
            });

            DB::statement('INSERT INTO attendances_new (id, event_id, user_id, scan_time, status, created_at, updated_at) SELECT id, event_id, user_id, scan_time, status, created_at, updated_at FROM attendances');
            Schema::drop('attendances');
            Schema::rename('attendances_new', 'attendances');
        } else {
            Schema::table('attendances', function (Blueprint $table) {
                $table->enum('status', ['Hadir', 'Izin', 'Sakit', 'Alpha', 'Terlambat'])->default('Alpha')->change();
                $table->text('keterangan')->nullable()->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE attendances DROP CONSTRAINT IF EXISTS attendances_status_check');
            DB::statement("ALTER TABLE attendances ALTER COLUMN status TYPE varchar(255)");
            DB::statement("ALTER TABLE attendances ALTER COLUMN status SET DEFAULT 'Alpha'");
            DB::statement("ALTER TABLE attendances ALTER COLUMN status SET NOT NULL");
            DB::statement("ALTER TABLE attendances ADD CONSTRAINT attendances_status_check CHECK (status IN ('Hadir', 'Izin', 'Sakit', 'Alpha'))");
            DB::statement("ALTER TABLE attendances DROP COLUMN IF EXISTS keterangan");
        } elseif (DB::getDriverName() === 'sqlite') {
            Schema::create('attendances_new', function (Blueprint $table) {
                $table->id();
                $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->dateTime('scan_time')->nullable();
                $table->enum('status', ['Hadir', 'Izin', 'Sakit', 'Alpha'])->default('Alpha');
                $table->timestamps();
            });

            DB::statement('INSERT INTO attendances_new (id, event_id, user_id, scan_time, status, created_at, updated_at) SELECT id, event_id, user_id, scan_time, status, created_at, updated_at FROM attendances');
            Schema::drop('attendances');
            Schema::rename('attendances_new', 'attendances');
        } else {
            Schema::table('attendances', function (Blueprint $table) {
                $table->dropColumn('keterangan');
                $table->enum('status', ['Hadir', 'Izin', 'Sakit', 'Alpha'])->default('Alpha')->change();
            });
        }
    }
};
