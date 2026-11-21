<?php

namespace Database\Factories;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition(): array
    {
        return [
            'event_id'   => \App\Models\Event::factory(),
            'user_id'    => \App\Models\User::factory(),
            'status'     => fake()->randomElement(['Hadir', 'Izin', 'Sakit', 'Alpha']),
            'scan_time'  => fake()->optional()->dateTimeBetween('-1 month', 'now'),
            'keterangan' => fake()->optional()->sentence(),
        ];
    }
}
