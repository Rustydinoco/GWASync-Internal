<?php

namespace App\Http\Controllers\Access;

use App\Http\Controllers\Controller;
use App\Models\WorkUnit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WorkUnitController extends Controller
{
    public function index()
    {
        return Inertia::render('Access/WorkUnits/Index', [
            'units' => WorkUnit::latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:work_units,code',
            'description' => 'nullable|string',
        ]);
        WorkUnit::create($validated);
        return redirect()->back()->with('success', 'Unit kerja berhasil ditambahkan.');
    }

    public function update(Request $request, WorkUnit $unit)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:work_units,code,' . $unit->id,
            'description' => 'nullable|string',
        ]);
        $unit->update($validated);
        return redirect()->back()->with('success', 'Unit kerja berhasil diperbarui.');
    }

    public function destroy(WorkUnit $unit)
    {
        $unit->delete();
        return redirect()->back()->with('success', 'Unit kerja berhasil dihapus.');
    }
}
