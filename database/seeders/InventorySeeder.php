<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inventory;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $inventories = [
            // --- BRASS ---
            ['item_code' => '102', 'name' => 'Trumpet', 'category' => 'Brass', 'condition' => 'Rusak Berat', 'status' => 'Maintenance'],
            ['item_code' => 'BRS-001', 'name' => 'Trumpet', 'category' => 'Brass', 'condition' => 'Rusak Berat', 'status' => 'Maintenance'],
            ['item_code' => '98080060', 'name' => 'Trumpet', 'category' => 'Brass', 'condition' => 'Rusak Berat', 'status' => 'Maintenance'],
            ['item_code' => '496525', 'name' => 'Trumpet', 'category' => 'Brass', 'condition' => 'Rusak Berat', 'status' => 'Maintenance'],
            ['item_code' => '496596', 'name' => 'Trumpet', 'category' => 'Brass', 'condition' => 'Rusak Berat', 'status' => 'Maintenance'],
            ['item_code' => 'BRS-002', 'name' => 'Case Trumphet 6', 'category' => 'Brass', 'condition' => 'Rusak Berat', 'status' => 'Maintenance'],
            ['item_code' => 'BRS-003', 'name' => 'Case Trumphet 5', 'category' => 'Brass', 'condition' => 'Rusak Berat', 'status' => 'Maintenance'],
            ['item_code' => '89090040', 'name' => 'Trumpet', 'category' => 'Brass', 'condition' => 'Rusak Berat', 'status' => 'Maintenance'],
            ['item_code' => '88280195', 'name' => 'Flugel', 'category' => 'Brass', 'condition' => 'Rusak Berat', 'status' => 'Maintenance'],
            ['item_code' => '88280180', 'name' => 'Flugel', 'category' => 'Brass', 'condition' => 'Rusak Berat', 'status' => 'Maintenance'],
            ['item_code' => '88280171', 'name' => 'Flugel', 'category' => 'Brass', 'condition' => 'Rusak Berat', 'status' => 'Maintenance'],
            ['item_code' => 'D07451', 'name' => 'Trombone', 'category' => 'Brass', 'condition' => 'Rusak Berat', 'status' => 'Maintenance'],
            ['item_code' => 'G02006', 'name' => 'Trombone', 'category' => 'Brass', 'condition' => 'Rusak Berat', 'status' => 'Maintenance'],
            ['item_code' => 'D06623', 'name' => 'Baritone', 'category' => 'Brass', 'condition' => 'Rusak Berat', 'status' => 'Maintenance'],
            ['item_code' => 'D09275', 'name' => 'Baritone', 'category' => 'Brass', 'condition' => 'Rusak Berat', 'status' => 'Maintenance'],
            ['item_code' => '44', 'name' => 'Euphonium', 'category' => 'Brass', 'condition' => 'Rusak Berat', 'status' => 'Maintenance'],
            ['item_code' => '82083', 'name' => 'Euphonium', 'category' => 'Brass', 'condition' => 'Rusak Berat', 'status' => 'Maintenance'],
            ['item_code' => 'BRS-004', 'name' => 'Baritone 01', 'category' => 'Brass', 'condition' => 'Rusak Berat', 'status' => 'Maintenance'],
            ['item_code' => 'BRS-005', 'name' => 'Baritone', 'category' => 'Brass', 'condition' => 'Rusak Berat', 'status' => 'Maintenance'],
            ['item_code' => '40', 'name' => 'Euphonium', 'category' => 'Brass', 'condition' => 'Rusak Berat', 'status' => 'Maintenance'],
            ['item_code' => 'BRS-006', 'name' => 'French horn', 'category' => 'Brass', 'condition' => 'Rusak Ringan', 'status' => 'Maintenance'],
            ['item_code' => '26', 'name' => 'Euphonium', 'category' => 'Brass', 'condition' => 'Rusak Berat', 'status' => 'Maintenance'],
            ['item_code' => '74467', 'name' => 'Euphonium', 'category' => 'Brass', 'condition' => 'Rusak Berat', 'status' => 'Maintenance'],
            ['item_code' => 'BRS-007', 'name' => 'French Horn', 'category' => 'Brass', 'condition' => 'Rusak Ringan', 'status' => 'Maintenance'],
            ['item_code' => 'BRS-008', 'name' => 'Tuba', 'category' => 'Brass', 'condition' => 'Rusak Berat', 'status' => 'Maintenance'],
            ['item_code' => 'BRS-009', 'name' => 'Tuba', 'category' => 'Brass', 'condition' => 'Rusak Berat', 'status' => 'Maintenance'],
            ['item_code' => 'BRS-010', 'name' => 'Trombone', 'category' => 'Brass', 'condition' => 'Rusak Berat', 'status' => 'Maintenance'],
            ['item_code' => 'BRS-011', 'name' => 'Case Trumpet', 'category' => 'Brass', 'condition' => 'Rusak Berat', 'status' => 'Maintenance'],
            ['item_code' => 'BRS-012', 'name' => 'Baritone', 'category' => 'Brass', 'condition' => 'Rusak Berat', 'status' => 'Maintenance'],
            ['item_code' => 'BRS-013', 'name' => 'Saxophone', 'category' => 'Brass', 'condition' => 'Baik', 'status' => 'Tersedia'],
            ['item_code' => 'BRS-014', 'name' => 'Mellophone', 'category' => 'Brass', 'condition' => 'Rusak Berat', 'status' => 'Maintenance'],
            ['item_code' => 'BRS-015', 'name' => 'Case MouthPiece', 'category' => 'Brass', 'condition' => 'Rusak Berat', 'status' => 'Maintenance'],
            ['item_code' => 'BRS-016', 'name' => 'Case Euphonium', 'category' => 'Brass', 'condition' => 'Rusak Berat', 'status' => 'Maintenance'],
            ['item_code' => 'BRS-017', 'name' => 'Trombone', 'category' => 'Brass', 'condition' => 'Rusak Berat', 'status' => 'Maintenance'],
            ['item_code' => 'BRS-018', 'name' => 'Trombone', 'category' => 'Brass', 'condition' => 'Rusak Berat', 'status' => 'Maintenance'],
            ['item_code' => 'BRS-019', 'name' => 'Trumpet Jupiter', 'category' => 'Brass', 'condition' => 'Baik', 'status' => 'Tersedia'],
            ['item_code' => 'BRS-020', 'name' => 'Mellophone Jupiter', 'category' => 'Brass', 'condition' => 'Baik', 'status' => 'Tersedia'],
            ['item_code' => 'BRS-021', 'name' => 'Baritone Jupiter', 'category' => 'Brass', 'condition' => 'Baik', 'status' => 'Tersedia'],
            ['item_code' => 'BRS-022', 'name' => 'Euphonium Jupiter', 'category' => 'Brass', 'condition' => 'Baik', 'status' => 'Tersedia'],
            ['item_code' => 'BRS-023', 'name' => 'Tuba Jupiter', 'category' => 'Brass', 'condition' => 'Baik', 'status' => 'Tersedia'],

            // --- BATTERY ---
            ['item_code' => '4', 'name' => 'Snare', 'category' => 'Battery', 'condition' => 'Rusak Berat', 'status' => 'Maintenance'],
            ['item_code' => '1', 'name' => 'Snare', 'category' => 'Battery', 'condition' => 'Rusak Berat', 'status' => 'Maintenance'],
            ['item_code' => '3', 'name' => 'Snare', 'category' => 'Battery', 'condition' => 'Rusak Berat', 'status' => 'Maintenance'],
            ['item_code' => '5', 'name' => 'Snare', 'category' => 'Battery', 'condition' => 'Rusak Berat', 'status' => 'Maintenance'],
            ['item_code' => '6', 'name' => 'Snare', 'category' => 'Battery', 'condition' => 'Rusak Ringan', 'status' => 'Maintenance'],
            ['item_code' => '2', 'name' => 'Snare', 'category' => 'Battery', 'condition' => 'Rusak Ringan', 'status' => 'Maintenance'],

            // --- COLOR GUARD ---
            ['item_code' => 'GRD-001', 'name' => 'Pole Flag', 'category' => 'Guard', 'condition' => 'Baik', 'status' => 'Tersedia'],
            ['item_code' => 'GRD-002', 'name' => 'Pole Flag', 'category' => 'Guard', 'condition' => 'Baik', 'status' => 'Tersedia'],
            ['item_code' => 'GRD-003', 'name' => 'Pole Flag', 'category' => 'Guard', 'condition' => 'Rusak Berat', 'status' => 'Maintenance'],
            ['item_code' => 'GRD-004', 'name' => 'Rifle', 'category' => 'Guard', 'condition' => 'Baik', 'status' => 'Tersedia'],
            ['item_code' => 'GRD-005', 'name' => 'Rifle', 'category' => 'Guard', 'condition' => 'Baik', 'status' => 'Tersedia'],
            ['item_code' => 'GRD-006', 'name' => 'Rifle', 'category' => 'Guard', 'condition' => 'Rusak Berat', 'status' => 'Maintenance'],
            ['item_code' => 'GRD-007', 'name' => 'Sabre', 'category' => 'Guard', 'condition' => 'Baik', 'status' => 'Tersedia'],
            ['item_code' => 'GRD-008', 'name' => 'Sabre', 'category' => 'Guard', 'condition' => 'Rusak Berat', 'status' => 'Maintenance'],
            ['item_code' => 'GRD-009', 'name' => 'Property Payung', 'category' => 'Guard', 'condition' => 'Baik', 'status' => 'Tersedia'],

            // --- PIT INSTRUMENT ---
            ['item_code' => 'PIT-001', 'name' => 'Marimba', 'category' => 'Pit_Instrument', 'condition' => 'Baik', 'status' => 'Tersedia'],
            ['item_code' => 'PIT-002', 'name' => 'Drum set', 'category' => 'Pit_Instrument', 'condition' => 'Baik', 'status' => 'Tersedia'],
            ['item_code' => 'PIT-003', 'name' => 'Gong', 'category' => 'Pit_Instrument', 'condition' => 'Baik', 'status' => 'Tersedia'],
            ['item_code' => 'PIT-004', 'name' => 'Stand Drum set', 'category' => 'Pit_Instrument', 'condition' => 'Baik', 'status' => 'Tersedia'],
            ['item_code' => 'PIT-005', 'name' => 'Bass Drum Konser', 'category' => 'Pit_Instrument', 'condition' => 'Baik', 'status' => 'Tersedia'],
            ['item_code' => 'PIT-006', 'name' => 'Vibra', 'category' => 'Pit_Instrument', 'condition' => 'Baik', 'status' => 'Tersedia'],
            ['item_code' => 'PIT-007', 'name' => 'Xylo', 'category' => 'Pit_Instrument', 'condition' => 'Baik', 'status' => 'Tersedia'],
            ['item_code' => 'PIT-008', 'name' => 'Glockenspiel', 'category' => 'Pit_Instrument', 'condition' => 'Baik', 'status' => 'Tersedia'],
            ['item_code' => 'PIT-009', 'name' => 'Timpani', 'category' => 'Pit_Instrument', 'condition' => 'Baik', 'status' => 'Tersedia'],
            ['item_code' => 'PIT-010', 'name' => 'Stand Pit', 'category' => 'Pit_Instrument', 'condition' => 'Baik', 'status' => 'Tersedia'],
            ['item_code' => 'PIT-011', 'name' => 'Bells', 'category' => 'Pit_Instrument', 'condition' => 'Baik', 'status' => 'Tersedia'],
            ['item_code' => 'PIT-012', 'name' => 'Rain stick', 'category' => 'Pit_Instrument', 'condition' => 'Baik', 'status' => 'Tersedia'],

            // --- PROPERTI (BUKAN ALAT) ---
            ['item_code' => 'PRP-001', 'name' => 'Lemari Kayu', 'category' => 'Properti', 'condition' => 'Rusak Berat', 'status' => 'Maintenance'],
            ['item_code' => 'PRP-002', 'name' => 'Lemari Kayu', 'category' => 'Properti', 'condition' => 'Baik', 'status' => 'Tersedia'],
            ['item_code' => 'PRP-003', 'name' => 'Lemari Besi', 'category' => 'Properti', 'condition' => 'Baik', 'status' => 'Tersedia'],
            ['item_code' => 'PRP-004', 'name' => 'Meja Kayu Coklat', 'category' => 'Properti', 'condition' => 'Baik', 'status' => 'Tersedia'],
            ['item_code' => 'PRP-005', 'name' => 'Meja Kayu Hitam', 'category' => 'Properti', 'condition' => 'Baik', 'status' => 'Tersedia'],
        ];

        foreach ($inventories as $item) {
            Inventory::create($item);
        }
    }
}