<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Letter extends Model
{
    use HasFactory;

    protected $fillable = [
        'letter_number',
        'title',
        'content',
        'recipient',
        'sender',
        'letter_date',
        'category',
        'created_by',
        'status',
    ];

    protected $casts = [
        'letter_date' => 'date',
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
