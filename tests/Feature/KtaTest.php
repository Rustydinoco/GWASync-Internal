<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KtaTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_access_kta(): void
    {
        $user = User::factory()->create(['role' => 'anggota']);

        $response = $this->actingAs($user)->get('/kta');

        $response->assertStatus(200);
    }

    public function test_management_can_access_kta(): void
    {
        $user = User::factory()->create(['role' => 'ketua_umum']);

        $response = $this->actingAs($user)->get('/kta');

        $response->assertStatus(200);
    }

    public function test_user_can_view_own_kta_only(): void
    {
        $user = User::factory()->create(['role' => 'anggota']);
        $otherUser = User::factory()->create(['role' => 'anggota']);

        // User can view their own KTA
        $response = $this->actingAs($user)->get('/kta?user_id=' . $user->id);
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->where('member.id', $user->id));

        // User cannot view another member's KTA (should be redirected to own)
        $response = $this->actingAs($user)->get('/kta?user_id=' . $otherUser->id);
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->where('member.id', $user->id));
    }
}
