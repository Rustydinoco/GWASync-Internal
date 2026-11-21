<?php

namespace App\Http\Controllers\Access;

use App\Http\Controllers\Controller;
use App\Models\StaffPosition;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StaffPositionController extends Controller
{
    public function index()
    {
        return Inertia::render('Access/StaffPositions/Index', [
            'positions' => StaffPosition::latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:staff_positions,code',
            'description' => 'nullable|string',
        ]);
        StaffPosition::create($validated);
        return redirect()->back()->with('success', 'Jabatan berhasil ditambahkan.');
    }

    public function update(Request $request, StaffPosition $position)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:staff_positions,code,' . $position->id,
            'description' => 'nullable|string',
        ]);
        $position->update($validated);
        return redirect()->back()->with('success', 'Jabatan berhasil diperbarui.');
    }

    public function destroy(StaffPosition $position)
    {
        $position->delete();
        return redirect()->back()->with('success', 'Jabatan berhasil dihapus.');
    }
}
