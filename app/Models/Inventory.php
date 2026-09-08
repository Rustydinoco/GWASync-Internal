<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'item_code',
        'category',
        'condition',
        'status'
    ];

    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class, 'inventory_id');
    }
}
