<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('letter_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('letter_id')->constrained('letters')->cascadeOnDelete();
            $table->foreignId('approver_user_id')->constrained('users');
            $table->integer('approval_level');
            $table->foreignId('flow_step_id')->nullable()->constrained('approval_flows')->nullOnDelete();
            $table->enum('decision', ['Setuju', 'Tolak', 'Revisi'])->default('Setuju');
            $table->string('note', 500)->nullable();
            $table->datetime('decided_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letter_approvals');
    }
};
