<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SponsorshipPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'name',
        'min_amount',
        'benefits',
        'quota',
        'seq_no',
    ];

    protected $casts = [
        'min_amount' => 'decimal:2',
        'quota' => 'integer',
        'seq_no' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function activity()
    {
        return $this->belongsTo(Event::class);
    }
}
