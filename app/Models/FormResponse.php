<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FormResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id',
        'member_id',
        'respondent_name',
        'respondent_email',
        'answers',
    ];

    protected $casts = [
        'answers' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }
}
