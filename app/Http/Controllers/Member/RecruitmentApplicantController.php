<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\RecruitmentApplicant;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RecruitmentApplicantController extends Controller
{
    public function index()
    {
        return Inertia::render('Member/RecruitmentApplicants/Index', [
            'applicants' => RecruitmentApplicant::with('recruitment', 'member')->latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'recruitment_id' => 'required|exists:recruitments,id',
            'member_id' => 'nullable|exists:users,id',
            'applicant_category' => 'required|in:Mahasiswa,Umum',
            'nim' => 'nullable|string|max:30',
            'fullname' => 'required|string|max:150',
            'email' => 'required|string|max:150',
            'phone' => 'nullable|string|max:30',
            'program_studi' => 'nullable|string|max:100',
            'angkatan' => 'nullable|integer',
            'institution' => 'nullable|string|max:150',
            'motivation' => 'nullable|text',
            'status' => 'required|in:Diajukan,Direview,Diterima,Ditolak',
            'reviewed_by' => 'nullable|exists:users,id',
            'reviewed_at' => 'nullable|date',
            'notes' => 'nullable|string|max:255',
        ]);
        RecruitmentApplicant::create($validated);
        return redirect()->back()->with('success', 'Perekrutan berhasil ditambahkan.');
    }

    public function update(Request $request, RecruitmentApplicant $applicant)
    {
        $validated = $request->validate([
            'recruitment_id' => 'required|exists:recruitments,id',
            'member_id' => 'nullable|exists:users,id',
            'applicant_category' => 'required|in:Mahasiswa,Umum',
            'nim' => 'nullable|string|max:30',
            'fullname' => 'required|string|max:150',
            'email' => 'required|string|max:150',
            'phone' => 'nullable|string|max:30',
            'program_studi' => 'nullable|string|max:100',
            'angkatan' => 'nullable|integer',
            'institution' => 'nullable|string|max:150',
            'motivation' => 'nullable|text',
            'status' => 'required|in:Diajukan,Direview,Diterima,Ditolak',
            'reviewed_by' => 'nullable|exists:users,id',
            'reviewed_at' => 'nullable|date',
            'notes' => 'nullable|string|max:255',
        ]);
        $applicant->update($validated);
        return redirect()->back()->with('success', 'Perekrutan berhasil diperbarui.');
    }

    public function destroy(RecruitmentApplicant $applicant)
    {
        $applicant->delete();
        return redirect()->back()->with('success', 'Perekrutan berhasil dihapus.');
    }
}
