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
                $query->where('name', 'ilike', "%{$search}%")
                      ->orWhere('nim', 'ilike', "%{$search}%")
                      ->orWhere('division', 'ilike', "%{$search}%");
            })
            ->latest()
            ->get();

        return Inertia::render('Members/Index', [
            'members' => $members,
            'filters' => ['search' => $search]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|string',
            'division' => 'nullable|in:Humas,Latbang,Sarpras,Medkraf,BPH,Usaha & Kemitraan,DPA',
            'status'   => 'required|in:Aktif,Pasif,Alumni',
        ]);

        // Default password untuk anggota baru
        $validated['password'] = Hash::make('mbgwa123'); 

        User::create($validated);
        return redirect()->back();
    }

    public function update(Request $request, User $member)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $member->id,
            'phone'    => 'nullable|string',
            'division' => 'nullable|in:Humas,Latbang,Sarpras,Medkraf,BPH',
            'status'   => 'required|in:Aktif,Pasif,Alumni',
        ]);

        $member->update($validated);
        return redirect()->back();
    }

    public function destroy(User $member)
    {
        $member->delete();
        return redirect()->back();
    }
}