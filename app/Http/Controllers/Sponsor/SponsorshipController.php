<?php

namespace App\Http\Controllers\Sponsor;

use App\Http\Controllers\Controller;
use App\Models\Sponsorship;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SponsorshipController extends Controller
{
    public function index()
    {
        return Inertia::render('Sponsor/Sponsorships/Index', [
            'sponsorships' => Sponsorship::latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'partner_name' => 'required|string|max:150',
            'contact_person' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:15',
            'email' => 'nullable|string|max:100',
            'package_id' => 'nullable|exists:sponsorship_packages,id',
            'amount' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'agreement_number' => 'nullable|string|max:100',
            'letter_id' => 'nullable|exists:letters,id',
            'signed_by_member_id' => 'nullable|exists:users,id',
            'status' => 'required|in:Prospek,Negosiasi,Deal,Ditolak',
        ]);
        Sponsorship::create($validated);
        return redirect()->back()->with('success', 'Sponsorship berhasil ditambahkan.');
    }

    public function update(Request $request, Sponsorship $sponsorship)
    {
        $validated = $request->validate([
            'partner_name' => 'required|string|max:150',
            'contact_person' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:15',
            'email' => 'nullable|string|max:100',
            'package_id' => 'nullable|exists:sponsorship_packages,id',
            'amount' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'agreement_number' => 'nullable|string|max:100',
            'letter_id' => 'nullable|exists:letters,id',
            'signed_by_member_id' => 'nullable|exists:users,id',
            'status' => 'required|in:Prospek,Negosiasi,Deal,Ditolak',
        ]);
        $sponsorship->update($validated);
        return redirect()->back()->with('success', 'Sponsorship berhasil diperbarui.');
    }

    public function destroy(Sponsorship $sponsorship)
    {
        $sponsorship->delete();
        return redirect()->back()->with('success', 'Sponsorship berhasil dihapus.');
    }
}
