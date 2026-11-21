<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        return [
            'title'       => fake()->sentence(),
            'description' => fake()->paragraph(),
            'type'        => fake()->randomElement(['Latihan', 'TC', 'Gladi', 'Kejuaraan', 'Rapat']),
            'start_time'  => fake()->dateTimeBetween('+1 day', '+1 month'),
            'end_time'    => fake()->dateTimeBetween('+2 days', '+2 month'),
            'location'    => fake()->address(),
            'qr_token'    => fake()->regexify('[A-Za-z0-9]{32}'),
        ];
    }
}
