<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Filament\Panel;
use Filament\Models\Contracts\FilamentUser;


class User extends Authenticatable implements FilamentUser
{

    use HasFactory, Notifiable;

    protected $fillable = [
        'nia',
        'name',
        'email',
        'password',
        'role',
        'phone',
        'kta_qr_code',
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class, 'user_id');
    }

    public function approvedBorrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class, 'approved_by');
    }

    public function verifiedPartnerships(): HasMany
    {
        return $this->hasMany(Partnership::class, 'verified_by');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'user_id');
    }

    public function verifiedAttendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'verified_by');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return in_array($this->role, ['ketua_umum','pengurus','anggota']);
    }

}
