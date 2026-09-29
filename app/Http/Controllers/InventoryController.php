<?php
namespace App\Http\Controllers;

use App\Models\Inventory;
use Inertia\Inertia;
use Illuminate\Http\Request;

class InventoryController extends Controller {
    public function index() {
        
        $search = request()->input('search');
        $inventories = Inventory::when($search, function ($query, $search) {
                    return $query->where('name', 'like', "%{$search}%")
                                ->orWhere('item_code', 'like', "%{$search}%")
                                ->orWhere('category', 'like', "%{$search}%")
                                ->orWhere('condition', 'like', "%{$search}%")
                                ->orWhere('status', 'like', "%{$search}%");
                })->latest()->get();


        // Mengirim data ke file Vue: resources/js/Pages/Inventories/Index.vue
        return Inertia::render('Inventories/Index', [
            'inventories' => $inventories,
            'filters' => [$search = request()->input('search')],
        ]);
    }

    public function store(Request $request) {
        // Validasi data yang diterima dari form
        $validatedData = $request->validate([
            
            'name' => 'required|string|max:255',
            'item_code' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'condition' => 'required|string|max:255',
            'status' => 'required|string|max:255',
        ]);

        // Membuat inventaris baru
        Inventory::create($validatedData);

        // Redirect kembali ke halaman inventaris dengan pesan sukses
        return redirect()->back()->with('success', 'Inventory created successfully.');
    }

    public function destroy(int $id) {
        // Mencari inventaris berdasarkan ID
        $inventory = Inventory::findOrFail($id);

        // Menghapus inventaris
        $inventory->delete();

        // Redirect kembali ke halaman inventaris dengan pesan sukses
        return redirect()->back()->with('success', 'Inventory deleted successfully.');
    }

    public function update(Request $request, int $id) {
        // Validasi data yang diterima dari form
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'item_code' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'condition' => 'required|string|max:255',
            'status' => 'required|string|max:255',
        ]);

        // Mencari inventaris berdasarkan ID
        $inventory = Inventory::findOrFail($id);

        // Memperbarui data inventaris
        $inventory->update($validatedData);

        // Redirect kembali ke halaman inventaris dengan pesan sukses
        return redirect()->back()->with('success', 'Inventory updated successfully.');
    }
}