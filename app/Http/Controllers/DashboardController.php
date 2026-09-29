<?php

namespace App\Http\Controllers;
use App\Models\Inventory;
use App\Models\Borrowing;
use App\Models\User;
use Inertia\Inertia;


use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalMembers = User::where('role', 'anggota')->count();
        $activeBorrowings = Inventory::where('status', 'Dipinjam')->count();
        $damagedInventories = Inventory::whereIn('condition', ['Rusak Ringan', 'Rusak Berat'])->count();

        $pendingBorrowings = Borrowing::with('user', 'inventory')
            ->where('status', 'Pending')
            ->latest()
            ->get();

        return Inertia::render('Dashboards/Index', [
            'stats' => [
                'totalMembers' => $totalMembers,
                'activeBorrowings' => $activeBorrowings,
                'damagedInventories' => $damagedInventories,
            ],
            'pendingBorrowings' => $pendingBorrowings,
        ]);
    }
}
