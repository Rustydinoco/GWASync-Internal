<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttendanceSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'schedule_id',
        'name',
        'open_at',
        'close_at',
        'late_tolerance_minute',
        'location',
        'qr_token',
        'latitude',
        'longitude',
        'radius_meter',
        'status',
        'cancel_reason',
    ];

    protected $casts = [
        'late_tolerance_minute' => 'integer',
        'open_at' => 'datetime',
        'close_at' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'radius_meter' => 'integer',
        'status' => 'string',
        'cancel_reason' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
