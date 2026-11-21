<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Inventory;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;

class BorrowingController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Borrowing::with(['inventory', 'user'])
            ->when($search, function ($query, $search) {
                $term = '%' . strtolower($search) . '%';
                $query->where(function ($w) use ($term) {
                    $w->whereHas('user', function ($q) use ($term) {
                        $q->whereRaw('LOWER(name) LIKE ?', [$term]);
                    })->orWhereHas('inventory', function ($q) use ($term) {
                        $q->whereRaw('LOWER(name) LIKE ?', [$term])
                          ->orWhereRaw('LOWER(item_code) LIKE ?', [$term]);
                    });
                });
            })
            ->latest()
            ->get();

        // Anggota hanya bisa lihat peminjaman dirinya sendiri
        if (auth()->user()->role === 'anggota') {
            $query = $query->where('user_id', auth()->id());
        }

        $borrowings = $query;

        // Untuk inventories yang tersedia, hanya pengurus/ketua_umum yang lihat semua
        $users = User::select('id', 'name')->get();
        $inventories = Inventory::whereRaw('LOWER(status) = ?', ['tersedia'])->get();

        return Inertia::render('Borrowings/Index', [
            'borrowings'  => $borrowings,
            'inventories' => $inventories,
            'users'       => $users,
            'filters'     => ['search' => $search],
            'userRole'    => auth()->user()->role,
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

        $validated['status'] = 'Pending';

        Borrowing::create($validated);

        return redirect()->back()->with('success', 'Pengajuan peminjaman berhasil dibuat.');
    }

    public function destroy(Borrowing $borrowing)
    {
        $borrowing->delete();

        return redirect()->back()->with('success', 'Data peminjaman berhasil dihapus.');
    }

    public function updateStatus(Request $request, Borrowing $borrowing)
    {
        $validated = $request->validate([
            'status' => 'required|in:Pending,Disetujui,Ditolak,Dikembalikan'
        ]);

        $borrowing->update([
            'status'      => $validated['status'],
            'approved_by' => auth()->id() ?? 1,
        ]);

        if ($validated['status'] === 'Disetujui') {
            $borrowing->inventory?->update(['status' => 'Dipinjam']);
        } elseif ($validated['status'] === 'Dikembalikan') {
            $borrowing->inventory?->update(['status' => 'Tersedia']);
        }

        return redirect()->back()->with('success', 'Status peminjaman berhasil diperbarui!');
    }
}   
