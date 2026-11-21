<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RecruitmentApplicant extends Model
{
    use HasFactory;

    protected $fillable = [
        'recruitment_id',
        'member_id',
        'applicant_category',
        'nim',
        'fullname',
        'email',
        'phone',
        'program_studi',
        'angkatan',
        'institution',
        'motivation',
        'status',
        'reviewed_by',
        'reviewed_at',
        'notes',
    ];

    protected $casts = [
        'angkatan' => 'integer',
        'reviewed_at' => 'datetime',
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function recruitment()
    {
        return $this->belongsTo(Recruitment::class);
    }

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }
}
