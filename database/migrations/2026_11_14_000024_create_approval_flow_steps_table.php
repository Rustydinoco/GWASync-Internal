<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_flow_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flow_id')->constrained('approval_flows')->cascadeOnDelete();
            $table->integer('step_level');
            $table->string('step_name', 100);
            $table->enum('approver_type', ['Role', 'Jabatan', 'Pengguna', 'Unit Kerja'])->default('Role');
            $table->foreignId('role_id')->nullable()->constrained('roles')->nullOnDelete();
            $table->foreignId('staff_position_id')->nullable()->constrained('staff_positions')->nullOnDelete();
            $table->foreignId('work_unit_id')->nullable()->constrained('work_units')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('allow_revision', [0, 1])->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_flow_steps');
    }
};
