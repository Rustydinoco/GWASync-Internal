<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LetterDisposition extends Model
{
    use HasFactory;

    protected $fillable = [
        'letter_id',
        'from_user_id',
        'to_member_id',
        'instruction',
        'due_date',
        'status',
        'done_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'status' => 'string',
        'done_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function letter()
    {
        return $this->belongsTo(Letter::class);
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toMember()
    {
        return $this->belongsTo(User::class, 'to_member_id');
    }
}
