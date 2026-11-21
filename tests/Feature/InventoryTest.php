<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_management_can_access_inventory(): void
    {
        $user = User::factory()->create(['role' => 'ketua_umum']);

        $response = $this->actingAs($user)->get('/inventories');

        $response->assertStatus(200);
    }

    public function test_management_can_create_inventory(): void
    {
        $user = User::factory()->create(['role' => 'ketua_umum']);

        $response = $this->actingAs($user)->post('/inventories', [
            'name'     => 'Mellophone',
            'item_code'=> 'BRS-001',
            'category' => 'Brass',
            'condition'=> 'Baik',
            'status'   => 'Tersedia',
            'catatan'  => 'Baru',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('inventories', [
            'item_code' => 'BRS-001',
            'name'      => 'Mellophone',
        ]);
    }

    public function test_management_can_update_inventory(): void
    {
        $user = User::factory()->create(['role' => 'ketua_umum']);
        $inventory = Inventory::factory()->create();

        $response = $this->actingAs($user)->put('/inventories/' . $inventory->id, [
            'name'     => 'Mellophone Updated',
            'item_code'=> $inventory->item_code,
            'category' => 'Brass',
            'condition'=> 'Baik',
            'status'   => 'Dipinjam',
            'catatan'  => 'Updated',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('inventories', [
            'id'        => $inventory->id,
            'name'      => 'Mellophone Updated',
            'status'    => 'Dipinjam',
        ]);
    }

    public function test_management_can_delete_inventory(): void
    {
        $user = User::factory()->create(['role' => 'ketua_umum']);
        $inventory = Inventory::factory()->create();

        $response = $this->actingAs($user)->delete('/inventories/' . $inventory->id);

        $response->assertStatus(302);
        $this->assertDatabaseMissing('inventories', [
            'id' => $inventory->id,
        ]);
    }

    public function test_anggota_cannot_access_inventory_crud(): void
    {
        $user = User::factory()->create(['role' => 'anggota']);

        $response = $this->actingAs($user)->post('/inventories', [
            'name'     => 'Test',
            'item_code'=> 'TST-001',
            'category' => 'Brass',
            'condition'=> 'Baik',
            'status'   => 'Tersedia',
        ]);

        $response->assertStatus(302);
    }
}
