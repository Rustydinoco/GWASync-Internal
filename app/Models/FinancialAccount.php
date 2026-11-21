<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FinancialAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'account_type',
        'bank_name',
        'account_number',
        'account_holder',
        'opening_balance',
        'is_active',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
