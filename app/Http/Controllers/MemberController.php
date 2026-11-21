<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $members = User::when($search, function ($query, $search) {
                $term = '%' . strtolower($search) . '%';
                $query->where(function ($q) use ($term) {
                    $q->whereRaw('LOWER(name) LIKE ?', [$term])
                      ->orWhereRaw('LOWER(nim) LIKE ?', [$term])
                      ->orWhereRaw('LOWER(division) LIKE ?', [$term]);
                });
            })
            ->latest()
            ->get();

        return Inertia::render('Members/Index', [
            'members' => $members,
            'filters' => ['search' => $search],
            'userRole' => auth()->user()->role,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:100|unique:users,email',
            'nim'      => 'nullable|string|max:50|unique:users,nim',
            'phone'    => 'nullable|string|max:20',
            'division' => 'nullable|in:Humas,Latbang,Sarpras,Medkraf,BPH,Usaha & Kemitraan,DPA',
            'status'   => 'required|in:Aktif,Pasif,Alumni',
        ]);

        $validated['password'] = Hash::make('mbgwa123');
        $validated['role'] = 'anggota';

        User::create($validated);
        return redirect()->back()->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function update(Request $request, User $member)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:100|unique:users,email,' . $member->id,
            'nim'      => 'nullable|string|max:50|unique:users,nim,' . $member->id,
            'phone'    => 'nullable|string|max:20',
            'division' => 'nullable|in:Humas,Latbang,Sarpras,Medkraf,BPH,Usaha & Kemitraan,DPA',
            'status'   => 'required|in:Aktif,Pasif,Alumni',
        ]);

        $member->update($validated);
        return redirect()->back()->with('success', 'Data anggota berhasil diperbarui.');
    }

    public function destroy(User $member)
    {
        $member->delete();
        return redirect()->back()->with('success', 'Anggota berhasil dihapus.');
    }
}
