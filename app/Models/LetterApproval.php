<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LetterApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'letter_id',
        'approver_user_id',
        'approval_level',
        'flow_step_id',
        'decision',
        'note',
        'decided_at',
    ];

    protected $casts = [
        'approval_level' => 'integer',
        'decision' => 'string',
        'decided_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function letter()
    {
        return $this->belongsTo(Letter::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_user_id');
    }
}
