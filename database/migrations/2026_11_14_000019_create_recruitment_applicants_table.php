<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recruitment_applicants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recruitment_id')->constrained('recruitments')->cascadeOnDelete();
            $table->foreignId('member_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('applicant_category', ['Mahasiswa', 'Umum'])->default('Mahasiswa');
            $table->string('nim', 30)->nullable();
            $table->string('fullname', 150);
            $table->string('email', 150);
            $table->string('phone', 30)->nullable();
            $table->string('program_studi', 100)->nullable();
            $table->smallInteger('angkatan')->nullable();
            $table->string('institution', 150)->nullable();
            $table->text('motivation')->nullable();
            $table->enum('status', ['Diajukan', 'Direview', 'Diterima', 'Ditolak'])->default('Diajukan');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->datetime('reviewed_at')->nullable();
            $table->string('notes', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recruitment_applicants');
    }
};
