<?php

namespace App\Http\Controllers\Sponsor;

use App\Http\Controllers\Controller;
use App\Models\PartnershipAgreement;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PartnershipAgreementController extends Controller
{
    public function index()
    {
        return Inertia::render('Sponsor/PartnershipAgreements/Index', [
            'agreements' => PartnershipAgreement::latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'organization_name' => 'required|string|max:150',
            'partner_name' => 'required|string|max:150',
            'agreement_number' => 'required|string|max:100|unique:partnership_agreements,agreement_number',
            'title' => 'required|string|max:200',
            'agreement_type' => 'required|in:MoU,PKS,Lainnya',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'letter_id' => 'nullable|exists:letters,id',
            'signed_by_member_id' => 'nullable|exists:users,id',
            'status' => 'required|in:Aktif,Berakhir,Dibatalkan',
        ]);
        PartnershipAgreement::create($validated);
        return redirect()->back()->with('success', 'Persetujuan kerjasama berhasil ditambahkan.');
    }

    public function update(Request $request, PartnershipAgreement $agreement)
    {
        $validated = $request->validate([
            'organization_name' => 'required|string|max:150',
            'partner_name' => 'required|string|max:150',
            'agreement_number' => 'required|string|max:100|unique:partnership_agreements,agreement_number,' . $agreement->id,
            'title' => 'required|string|max:200',
            'agreement_type' => 'required|in:MoU,PKS,Lainnya',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'letter_id' => 'nullable|exists:letters,id',
            'signed_by_member_id' => 'nullable|exists:users,id',
            'status' => 'required|in:Aktif,Berakhir,Dibatalkan',
        ]);
        $agreement->update($validated);
        return redirect()->back()->with('success', 'Persetujuan kerjasama berhasil diperbarui.');
    }

    public function destroy(PartnershipAgreement $agreement)
    {
        $agreement->delete();
        return redirect()->back()->with('success', 'Persetujuan kerjasama berhasil dihapus.');
    }
}
