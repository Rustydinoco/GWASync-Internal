<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Recruitment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RecruitmentController extends Controller
{
    public function index()
    {
        return Inertia::render('Member/Recruitments/Index', [
            'recruitments' => Recruitment::with('activity')->latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'activity_id' => 'required|exists:events,id',
            'open_at' => 'required|date',
            'close_at' => 'required|date',
            'quota' => 'nullable|integer|min:0',
            'form_id' => 'nullable|exists:forms,id',
            'status' => 'required|in:Buka,Tutup,Selesai',
        ]);
        Recruitment::create($validated);
        return redirect()->back()->with('success', 'Rekrutmen berhasil ditambahkan.');
    }

    public function update(Request $request, Recruitment $recruitment)
    {
        $validated = $request->validate([
            'activity_id' => 'required|exists:events,id',
            'open_at' => 'required|date',
            'close_at' => 'required|date',
            'quota' => 'nullable|integer|min:0',
            'form_id' => 'nullable|exists:forms,id',
            'status' => 'required|in:Buka,Tutup,Selesai',
        ]);
        $recruitment->update($validated);
        return redirect()->back()->with('success', 'Rekrutmen berhasil diperbarui.');
    }

    public function destroy(Recruitment $recruitment)
    {
        $recruitment->delete();
        return redirect()->back()->with('success', 'Rekrutmen berhasil dihapus.');
    }
}
