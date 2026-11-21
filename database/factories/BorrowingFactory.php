<?php

namespace Database\Factories;

use App\Models\Borrowing;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Borrowing>
 */
class BorrowingFactory extends Factory
{
    protected $model = Borrowing::class;

    public function definition(): array
    {
        return [
            'user_id'    => \App\Models\User::factory(),
            'inventory_id'=> \App\Models\Inventory::factory(),
            'start_date' => fake()->date('Y-m-d', '+1 month'),
            'end_date'   => fake()->date('Y-m-d', '+3 month'),
            'status'     => fake()->randomElement(['Pending', 'Disetujui', 'Ditolak', 'Dikembalikan']),
        ];
    }
}
