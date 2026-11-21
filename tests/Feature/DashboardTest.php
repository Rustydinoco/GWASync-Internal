<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
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

    public function test_dashboard_shows_statistics(): void
    {
        $user = User::factory()->create(['role' => 'ketua_umum']);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->has('stats'));
        $response->assertInertia(fn ($page) => $page->has('userRole'));
    }

    public function test_dashboard_quick_actions_visible_for_all_roles(): void
    {
        $roles = ['ketua_umum', 'pengurus', 'anggota'];

        foreach ($roles as $role) {
            $user = User::factory()->create(['role' => $role]);
            $response = $this->actingAs($user)->get('/dashboard');
            $response->assertStatus(200);
            $response->assertInertia(fn ($page) => $page->has('stats'));
        }
    }

    public function test_dashboard_accessible_by_all_roles(): void
    {
        $roles = ['ketua_umum', 'pengurus', 'anggota', 'calon'];

        foreach ($roles as $role) {
            $user = User::factory()->create(['role' => $role]);
            $response = $this->actingAs($user)->get('/dashboard');
            $response->assertStatus(200);
        }
    }
}
