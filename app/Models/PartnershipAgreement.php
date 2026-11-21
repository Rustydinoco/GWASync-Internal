<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PartnershipAgreement extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_name',
        'partner_name',
        'agreement_number',
        'title',
        'agreement_type',
        'start_date',
        'end_date',
        'letter_id',
        'document',
        'document_file_name',
        'document_content_type',
        'signed_by_member_id',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'agreement_type' => 'string',
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function letter()
    {
        return $this->belongsTo(Letter::class);
    }

    public function signedBy()
    {
        return $this->belongsTo(User::class, 'signed_by_member_id');
    }
}
