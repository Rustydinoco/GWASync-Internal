<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_access_attendances(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/attendances');

        $response->assertStatus(200);
    }

    public function test_user_can_scan_attendance(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(['qr_token' => 'test_qr_token_123']);

        $response = $this->actingAs($user)->post('/attendances/scan', [
            'qr_token' => 'test_qr_token_123',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('attendances', [
            'event_id' => $event->id,
            'user_id'  => $user->id,
            'status'   => 'Hadir',
        ]);
    }

    public function test_invalid_qr_token_rejected(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/attendances/scan', [
            'qr_token' => 'invalid_token',
        ]);

        $response->assertStatus(422);
    }

    public function test_management_can_mark_attendance_manual(): void
    {
        $user = User::factory()->create(['role' => 'ketua_umum']);
        $member = User::factory()->create(['role' => 'anggota']);
        $event = Event::factory()->create();

        $response = $this->actingAs($user)->post('/attendances', [
            'event_id' => $event->id,
            'user_id'  => $member->id,
            'status'   => 'Hadir',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('attendances', [
            'event_id' => $event->id,
            'user_id'  => $member->id,
            'status'   => 'Hadir',
        ]);
    }

    public function test_management_can_update_attendance(): void
    {
        $user = User::factory()->create(['role' => 'ketua_umum']);
        $attendance = Attendance::factory()->create(['status' => 'Alpha']);

        $response = $this->actingAs($user)->patch('/attendances/' . $attendance->id, [
            'status' => 'Hadir',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('attendances', [
            'id'    => $attendance->id,
            'status'=> 'Hadir',
        ]);
    }

    public function test_management_can_populate_attendance(): void
    {
        $user = User::factory()->create(['role' => 'ketua_umum', 'status' => 'Aktif']);
        $event = Event::factory()->create();
        $activeUsers = User::factory()->count(5)->create(['status' => 'Aktif']);

        $response = $this->actingAs($user)->post('/attendances/events/' . $event->id . '/populate');

        $response->assertStatus(302);
        // Should create attendance for 5 active users (not including the ketua_umum who may also be Aktif)
        $this->assertGreaterThanOrEqual(5, \App\Models\Attendance::where('event_id', $event->id)->count());
    }
}
