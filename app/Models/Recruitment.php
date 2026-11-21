<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Recruitment extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'open_at',
        'close_at',
        'quota',
        'form_id',
        'status',
    ];

    protected $casts = [
        'open_at' => 'datetime',
        'close_at' => 'datetime',
        'quota' => 'integer',
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function activity()
    {
        return $this->belongsTo(Event::class);
    }

    public function form()
    {
        return $this->belongsTo(Form::class);
    }
}
