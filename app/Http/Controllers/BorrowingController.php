<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Inventory;
use App\Models\User;
use inertia\Inertia;
use Illuminate\Http\Request;

class BorrowingController extends Controller
{
    public function index(Request $request)
{
    $search = $request->input('search');

    // Filter pencarian pada tabel borrowings, user, dan inventory
    $borrowings = Borrowing::with(['inventory', 'user'])
        ->when($search, function ($query, $search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%");
            })->orWhereHas('inventory', function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                 ->orWhere('item_code', 'ilike', "%{$search}%");
            });
        })
        ->latest()
        ->get();

    // Optimasi: Cukup ambil ID dan nama untuk dropdown
    $users = User::select('id', 'name')->get(); 
    $inventories = Inventory::whereRaw('LOWER(status) = ?', ['tersedia'])->get();

    return Inertia::render('Borrowings/Index', [
        'borrowings'  => $borrowings,
        'inventories' => $inventories,
        'users'       => $users,
        'filters'     => ['search' => $search], // Kirim parameter kembali ke Vue
    ]);
}

    public function store(Request $request)
    {
       $validated = $request->validate([
        'user_id'      => 'required|exists:users,id',
        'inventory_id' => 'required|exists:inventories,id',
        'start_date'   => 'required|date',
        'end_date'     => 'required|date|after_or_equal:start_date',
    ]);

    // 2. Set status awal pengajuan
    $validated['status'] = 'Pending';

    // 3. Simpan ke database
    Borrowing::create($validated);

    return redirect()->back()->with('success', 'Pengajuan peminjaman berhasil dibuat.');
    }

    public function destroy($id)
    {
        $borrowing = Borrowing::findOrFail($id);
        $borrowing->delete();

        return redirect()->back()->with('success', 'Borrowing deleted successfully.');
    }


    public function updateStatus(Request $request, Borrowing $borrowing)
    {
        $validated = $request->validate([
            'status' => 'required|in:Pending,Disetujui,Ditolak,Dikembalikan'
        ]);

        // Simpan status baru dan catat ID pengurus yang menyetujui
        $borrowing->update([
            'status'      => $validated['status'],
            'approved_by' => 1 // Ganti dengan auth()->id() jika fitur login sudah aktif
        ]);

        // Otomatis ubah status ketersediaan alat di tabel Inventories
        if ($validated['status'] === 'Disetujui') {
            $borrowing->inventory->update(['status' => 'Dipinjam']);
        } elseif ($validated['status'] === 'Dikembalikan') {
            $borrowing->inventory->update(['status' => 'Tersedia']);
        }

        return redirect()->back()->with('success', 'Status peminjaman berhasil diperbarui!');
    }
}   
