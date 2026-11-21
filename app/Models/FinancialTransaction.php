<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FinancialTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'account_id',
        'category_id',
        'period_id',
        'budget_item_id',
        'funding_request_id',
        'activity_id',
        'program_id',
        'transaction_number',
        'transaction_type',
        'amount',
        'transaction_date',
        'description',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'status' => 'string',
        'transaction_date' => 'date',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function account()
    {
        return $this->belongsTo(FinancialAccount::class);
    }

    public function category()
    {
        return $this->belongsTo(FinancialCategory::class);
    }
}
