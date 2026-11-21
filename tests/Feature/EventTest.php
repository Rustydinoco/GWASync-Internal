<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_access_events(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/events');

        $response->assertStatus(200);
    }

    public function test_management_can_create_event(): void
    {
        $user = User::factory()->create(['role' => 'ketua_umum']);

        $response = $this->actingAs($user)->post('/events', [
            'title'       => 'Latihan Rutin Brass',
            'description' => 'Materi pemanasan dan visual brass.',
            'type'        => 'Latihan',
            'start_time'  => '2026-11-10 16:00:00',
            'end_time'    => '2026-11-10 18:00:00',
            'location'    => 'Gedung Olahraga Kampus',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('events', [
            'title' => 'Latihan Rutin Brass',
            'type'  => 'Latihan',
        ]);
    }

    public function test_management_can_delete_event(): void
    {
        $user = User::factory()->create(['role' => 'ketua_umum']);
        $event = Event::factory()->create();

        $response = $this->actingAs($user)->delete('/events/' . $event->id);

        $response->assertStatus(302);
        $this->assertDatabaseMissing('events', [
            'id' => $event->id,
        ]);
    }

    public function test_anggota_cannot_create_event(): void
    {
        $user = User::factory()->create(['role' => 'anggota']);

        $response = $this->actingAs($user)->post('/events', [
            'title'       => 'Test Event',
            'type'        => 'Latihan',
            'start_time'  => '2026-11-10 16:00:00',
            'end_time'    => '2026-11-10 18:00:00',
            'location'    => 'Test Location',
        ]);

        $response->assertStatus(302);
    }

    public function test_event_attendances_endpoint_accessible(): void
    {
        $user = User::factory()->create(['role' => 'ketua_umum']);
        $event = Event::factory()->create();

        $response = $this->actingAs($user)->get('/events/' . $event->id . '/attendances');

        $response->assertStatus(200);
    }
}
