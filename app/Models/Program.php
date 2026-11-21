<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'organization_id',
        'period_id',
        'start_date',
        'end_date',
        'total_budget',
        'requested_campus_fund',
        'approved_campus_fund',
        'status',
    ];

    protected $casts = [
        'total_budget' => 'decimal:2',
        'requested_campus_fund' => 'decimal:2',
        'approved_campus_fund' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function period()
    {
        return $this->belongsTo(ManagementPeriod::class);
    }
}
