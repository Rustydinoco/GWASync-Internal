<?php

namespace App\Http\Controllers\Access;

use App\Http\Controllers\Controller;
use App\Models\ApprovalFlow;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ApprovalFlowController extends Controller
{
    public function index()
    {
        return Inertia::render('Access/ApprovalFlows/Index', [
            'flows' => ApprovalFlow::latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|in:0,1',
        ]);
        ApprovalFlow::create($validated);
        return redirect()->back()->with('success', 'Alur persetujuan berhasil ditambahkan.');
    }

    public function update(Request $request, ApprovalFlow $flow)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|in:0,1',
        ]);
        $flow->update($validated);
        return redirect()->back()->with('success', 'Alur persetujuan berhasil diperbarui.');
    }

    public function destroy(ApprovalFlow $flow)
    {
        $flow->delete();
        return redirect()->back()->with('success', 'Alur persetujuan berhasil dihapus.');
    }
}
