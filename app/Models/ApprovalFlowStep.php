<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApprovalFlowStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'flow_id',
        'step_level',
        'step_name',
        'approver_type',
        'role_id',
        'staff_position_id',
        'work_unit_id',
        'user_id',
        'allow_revision',
    ];

    protected $casts = [
        'step_level' => 'integer',
        'allow_revision' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function flow()
    {
        return $this->belongsTo(ApprovalFlow::class);
    }
}
