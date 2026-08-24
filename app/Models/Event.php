<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title',
        'type',
        'location',
        'status',
        'qr_code',

    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'event_id');
    }
}
