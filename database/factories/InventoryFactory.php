<?php

namespace Database\Factories;

use App\Models\Inventory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inventory>
 */
class InventoryFactory extends Factory
{
    protected $model = Inventory::class;

    public function definition(): array
    {
        return [
            'name'      => fake()->randomElement(['Mellophone', 'Snare Drum', 'Sousaphone', 'Cymbal', 'Flag', 'Bass Drum']),
            'item_code' => fake()->unique()->regexify('[A-Z]{3}-\d{3}'),
            'category'  => fake()->randomElement(['Brass', 'Battery', 'Pit_Instrument', 'Guard', 'Properti']),
            'condition' => fake()->randomElement(['Baik', 'Rusak Ringan', 'Rusak Berat']),
            'status'    => fake()->randomElement(['Tersedia', 'Dipinjam', 'Maintenance']),
            'catatan'   => fake()->optional()->sentence(),
        ];
    }
}
