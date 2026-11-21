<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FundingRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'program_id',
        'activity_id',
        'request_number',
        'title',
        'reason',
        'total_estimate',
        'total_actual',
        'needed_by_date',
        'payment_method',
        'status',
        'reject_reason',
    ];

    protected $casts = [
        'total_estimate' => 'decimal:2',
        'total_actual' => 'decimal:2',
        'needed_by_date' => 'date',
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
