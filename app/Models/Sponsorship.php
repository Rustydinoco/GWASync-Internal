<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sponsorship extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_name',
        'contact_person',
        'phone',
        'email',
        'package_id',
        'amount',
        'start_date',
        'end_date',
        'agreement_number',
        'letter_id',
        'document',
        'document_file_name',
        'document_content_type',
        'signed_by_member_id',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function package()
    {
        return $this->belongsTo(SponsorshipPackage::class);
    }

    public function letter()
    {
        return $this->belongsTo(Letter::class);
    }

    public function signedBy()
    {
        return $this->belongsTo(User::class, 'signed_by_member_id');
    }
}
