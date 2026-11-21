<?php

namespace App\Http\Controllers\Access;

use App\Http\Controllers\Controller;
use App\Models\ApprovalFlowStep;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ApprovalFlowStepController extends Controller
{
    public function index()
    {
        return Inertia::render('Access/ApprovalFlowSteps/Index', [
            'steps' => ApprovalFlowStep::with('flow')->latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'flow_id' => 'required|exists:approval_flows,id',
            'step_level' => 'required|integer',
            'step_name' => 'required|string|max:100',
            'approver_type' => 'required|in:Role,Jabatan,Pengguna,Unit Kerja',
            'role_id' => 'nullable|exists:roles,id',
            'staff_position_id' => 'nullable|exists:staff_positions,id',
            'work_unit_id' => 'nullable|exists:work_units,id',
            'user_id' => 'nullable|exists:users,id',
            'allow_revision' => 'sometimes|in:0,1',
        ]);
        ApprovalFlowStep::create($validated);
        return redirect()->back()->with('success', 'Langkah persetujuan berhasil ditambahkan.');
    }

    public function update(Request $request, ApprovalFlowStep $step)
    {
        $validated = $request->validate([
            'flow_id' => 'required|exists:approval_flows,id',
            'step_level' => 'required|integer',
            'step_name' => 'required|string|max:100',
            'approver_type' => 'required|in:Role,Jabatan,Pengguna,Unit Kerja',
            'role_id' => 'nullable|exists:roles,id',
            'staff_position_id' => 'nullable|exists:staff_positions,id',
            'work_unit_id' => 'nullable|exists:work_units,id',
            'user_id' => 'nullable|exists:users,id',
            'allow_revision' => 'sometimes|in:0,1',
        ]);
        $step->update($validated);
        return redirect()->back()->with('success', 'Langkah persetujuan berhasil diperbarui.');
    }

    public function destroy(ApprovalFlowStep $step)
    {
        $step->delete();
        return redirect()->back()->with('success', 'Langkah persetujuan berhasil dihapus.');
    }
}
