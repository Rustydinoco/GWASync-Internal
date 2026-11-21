<?php

namespace App\Http\Controllers\Letter;

use App\Http\Controllers\Controller;
use App\Models\LetterApproval;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LetterApprovalController extends Controller
{
    public function index()
    {
        return Inertia::render('Letter/Approvals/Index', [
            'approvals' => LetterApproval::with('letter', 'approver')->latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'letter_id' => 'required|exists:letters,id',
            'approver_user_id' => 'required|exists:users,id',
            'approval_level' => 'required|integer',
            'flow_step_id' => 'nullable|exists:approval_flows,id',
            'decision' => 'required|in:Setuju,Tolak,Revisi',
            'note' => 'nullable|string|max:500',
            'decided_at' => 'nullable|date',
        ]);
        LetterApproval::create($validated);
        return redirect()->back()->with('success', 'Persetujuan surat berhasil ditambahkan.');
    }

    public function update(Request $request, LetterApproval $approval)
    {
        $validated = $request->validate([
            'letter_id' => 'required|exists:letters,id',
            'approver_user_id' => 'required|exists:users,id',
            'approval_level' => 'required|integer',
            'flow_step_id' => 'nullable|exists:approval_flows,id',
            'decision' => 'required|in:Setuju,Tolak,Revisi',
            'note' => 'nullable|string|max:500',
            'decided_at' => 'nullable|date',
        ]);
        $approval->update($validated);
        return redirect()->back()->with('success', 'Persetujuan surat berhasil diperbarui.');
    }

    public function destroy(LetterApproval $approval)
    {
        $approval->delete();
        return redirect()->back()->with('success', 'Persetujuan surat berhasil dihapus.');
    }
}
