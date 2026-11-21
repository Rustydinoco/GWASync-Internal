<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberTest extends TestCase
{
    use RefreshDatabase;

    public function test_ketua_umum_can_access_members(): void
    {
        $user = User::factory()->create(['role' => 'ketua_umum']);

        $response = $this->actingAs($user)->get('/members');

        $response->assertStatus(200);
    }

    public function test_ketua_umum_can_create_member(): void
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
            'role'  => 'anggota',
        ]);
    }

    public function test_pengurus_cannot_access_members(): void
    {
        $user = User::factory()->create(['role' => 'pengurus']);

        $response = $this->actingAs($user)->post('/members', [
            'name'     => 'Test',
            'email'    => 'test@test.com',
            'nim'      => '123456',
        ]);

        $response->assertStatus(302);
    }

    public function test_anggota_cannot_access_members(): void
    {
        $user = User::factory()->create(['role' => 'anggota']);

        $response = $this->actingAs($user)->get('/members');

        $response->assertStatus(302);
    }
}
