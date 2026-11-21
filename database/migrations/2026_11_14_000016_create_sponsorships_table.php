<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sponsorships', function (Blueprint $table) {
            $table->id();
            $table->string('partner_name', 150);
            $table->string('contact_person', 100)->nullable();
            $table->string('phone', 15)->nullable();
            $table->string('email', 100)->nullable();
            $table->foreignId('package_id')->nullable()->constrained('sponsorship_packages')->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('agreement_number', 100)->nullable();
            $table->foreignId('letter_id')->nullable()->constrained('letters')->nullOnDelete();
            $table->foreignId('document')->nullable();
            $table->string('document_file_name', 255)->nullable();
            $table->string('document_content_type', 100)->nullable();
            $table->foreignId('signed_by_member_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['Prospek', 'Negosiasi', 'Deal', 'Ditolak'])->default('Prospek');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sponsorships');
    }
};
