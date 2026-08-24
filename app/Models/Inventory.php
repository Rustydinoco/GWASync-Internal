<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillabble = [
        'name',
        'item_code',
        'name',
        'category',
        'condition',
        'status'
    ];

    public function borrowings()
    {
        return $this->hasMany(Borrowing::class, 'inventory_id');
    }
}
