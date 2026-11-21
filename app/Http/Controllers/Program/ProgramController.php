<?php

namespace App\Http\Controllers\Program;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProgramController extends Controller
{
    public function index()
    {
        return Inertia::render('Programs/Index', [
            'programs' => Program::with('period')->latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|text',
            'organization_id' => 'nullable|exists:users,id',
            'period_id' => 'nullable|exists:management_periods,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'total_budget' => 'nullable|numeric|min:0',
            'requested_campus_fund' => 'nullable|numeric|min:0',
            'approved_campus_fund' => 'nullable|numeric|min:0',
            'status' => 'required|in:Draft,Diajuan,Disetujui,Ditolak,Selesai',
        ]);
        Program::create($validated);
        return redirect()->back()->with('success', 'Program berhasil ditambahkan.');
    }

    public function update(Request $request, Program $program)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|text',
            'organization_id' => 'nullable|exists:users,id',
            'period_id' => 'nullable|exists:management_periods,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'total_budget' => 'nullable|numeric|min:0',
            'requested_campus_fund' => 'nullable|numeric|min:0',
            'approved_campus_fund' => 'nullable|numeric|min:0',
            'status' => 'required|in:Draft,Diajuan,Disetujui,Ditolak,Selesai',
        ]);
        $program->update($validated);
        return redirect()->back()->with('success', 'Program berhasil diperbarui.');
    }

    public function destroy(Program $program)
    {
        $program->delete();
        return redirect()->back()->with('success', 'Program berhasil dihapus.');
    }
}
