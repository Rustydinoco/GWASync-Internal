<?php
namespace App\Http\Controllers;

use App\Models\Inventory;
use Inertia\Inertia;
use Illuminate\Http\Request;

class InventoryController extends Controller {
    public function index(Request $request) {
        $search = $request->input('search');
        $inventories = Inventory::when($search, function ($query, $search) {
                    $term = '%' . strtolower($search) . '%';
                    return $query->where(function ($q) use ($term) {
                        $q->whereRaw('LOWER(name) LIKE ?', [$term])
                          ->orWhereRaw('LOWER(item_code) LIKE ?', [$term])
                          ->orWhereRaw('LOWER(category) LIKE ?', [$term])
                          ->orWhereRaw('LOWER(condition) LIKE ?', [$term])
                          ->orWhereRaw('LOWER(status) LIKE ?', [$term])
                          ->orWhereRaw('LOWER(catatan) LIKE ?', [$term]);
                    });
                })->latest()->get();

        return Inertia::render('Inventories/Index', [
            'inventories' => $inventories,
            'filters' => ['search' => $search],
        ]);
    }

    public function store(Request $request) {
        $validatedData = $request->validate([
            'name'      => 'required|string|max:150',
            'item_code' => 'required|string|max:50|unique:inventories,item_code',
            'category'  => 'required|in:Brass,Battery,Pit_Instrument,Guard,Properti',
            'condition' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'status'    => 'required|in:Tersedia,Dipinjam,Maintenance',
            'catatan'   => 'nullable|string|max:255',
        ]);

        Inventory::create($validatedData);

        return redirect()->back()->with('success', 'Inventaris berhasil ditambahkan.');
    }

    public function destroy(Inventory $inventory) {
        $inventory->delete();

        return redirect()->back()->with('success', 'Inventaris berhasil dihapus.');
    }

    public function update(Request $request, Inventory $inventory) {
        $validatedData = $request->validate([
            'name'      => 'required|string|max:150',
            'item_code' => 'required|string|max:50|unique:inventories,item_code,' . $inventory->id,
            'category'  => 'required|in:Brass,Battery,Pit_Instrument,Guard,Properti',
            'condition' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'status'    => 'required|in:Tersedia,Dipinjam,Maintenance',
            'catatan'   => 'nullable|string|max:255',
        ]);

        $inventory->update($validatedData);

        return redirect()->back()->with('success', 'Inventaris berhasil diperbarui.');
    }
}