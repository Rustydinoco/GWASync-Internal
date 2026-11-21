<?php

namespace Tests\Feature;

use App\Models\Borrowing;
use App\Models\Inventory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BorrowingTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_access_borrowings(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/borrowings');

        $response->assertStatus(200);
    }

    public function test_user_can_create_borrowing(): void
    {
        $user = User::factory()->create();
        $inventory = Inventory::factory()->create();

        $response = $this->actingAs($user)->post('/borrowings', [
            'user_id'      => $user->id,
            'inventory_id' => $inventory->id,
            'start_date'   => '2026-11-10',
            'end_date'     => '2026-11-15',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('borrowings', [
            'user_id'     => $user->id,
            'inventory_id' => $inventory->id,
            'status'      => 'Pending',
        ]);
    }

    public function test_management_can_update_borrowing_status(): void
    {
        $user = User::factory()->create(['role' => 'ketua_umum']);
        $borrowing = Borrowing::factory()->create(['status' => 'Pending']);

        $response = $this->actingAs($user)->patch('/borrowings/' . $borrowing->id . '/status', [
            'status' => 'Disetujui',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('borrowings', [
            'id'       => $borrowing->id,
            'status'   => 'Disetujui',
        ]);
    }

    public function test_management_can_delete_borrowing(): void
    {
        $user = User::factory()->create(['role' => 'ketua_umum']);
        $borrowing = Borrowing::factory()->create();

        $response = $this->actingAs($user)->delete('/borrowings/' . $borrowing->id);

        $response->assertStatus(302);
        $this->assertDatabaseMissing('borrowings', [
            'id' => $borrowing->id,
        ]);
    }
}
