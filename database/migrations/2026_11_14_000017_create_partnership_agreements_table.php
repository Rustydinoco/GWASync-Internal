<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partnership_agreements', function (Blueprint $table) {
            $table->id();
            $table->string('organization_name', 150);
            $table->string('partner_name', 150);
            $table->string('agreement_number', 100)->unique();
            $table->string('title', 200);
            $table->enum('agreement_type', ['MoU', 'PKS', 'Lainnya'])->default('Lainnya');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->foreignId('letter_id')->nullable()->constrained('letters')->nullOnDelete();
            $table->foreignId('document')->nullable();
            $table->string('document_file_name', 255)->nullable();
            $table->string('document_content_type', 100)->nullable();
            $table->foreignId('signed_by_member_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['Aktif', 'Berakhir', 'Dibatalkan'])->default('Aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partnership_agreements');
    }
};
