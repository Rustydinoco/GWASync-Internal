<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'type',
        'start_time',
        'end_time',
        'location',
        'qr_token',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'event_id');
    }
}
