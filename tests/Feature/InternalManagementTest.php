<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Inventory;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InternalManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_guest_is_redirected_from_dashboard_to_login(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_member_can_be_created_with_nim(): void
    {
        $user = User::factory()->create(['role' => 'ketua_umum']);

        $response = $this->actingAs($user)->post('/members', [
            'name'     => 'Ahmad Syauqi',
            'email'    => 'ahmad@mbgwa.org',
            'nim'      => '21081010001',
            'phone'    => '081234567890',
            'division' => 'Latbang',
            'status'   => 'Aktif',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', [
            'email' => 'ahmad@mbgwa.org',
            'nim'   => '21081010001',
        ]);
    }

    public function test_event_can_be_created_with_dates_and_type(): void
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

        $response->assertRedirect('/events');
        $this->assertDatabaseHas('events', [
            'title' => 'Latihan Rutin Brass',
            'type'  => 'Latihan',
        ]);
    }

    public function test_attendance_and_kta_routes_are_accessible(): void
    {
        $user = User::factory()->create(['role' => 'pengurus']);

        $attendanceResponse = $this->actingAs($user)->get('/attendances');
        $attendanceResponse->assertStatus(200);

        $ktaResponse = $this->actingAs($user)->get('/kta');
        $ktaResponse->assertStatus(200);
    }
}
