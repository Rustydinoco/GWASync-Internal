<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->enum('account_type', ['Kas', 'Bank'])->default('Kas');
            $table->string('bank_name', 100)->nullable();
            $table->string('account_number', 50)->nullable();
            $table->string('account_holder', 150)->nullable();
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->enum('is_active', [0, 1])->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_accounts');
    }
};
