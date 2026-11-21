<?php

namespace App\Http\Controllers\Sponsor;

use App\Http\Controllers\Controller;
use App\Models\SponsorshipPackage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SponsorshipPackageController extends Controller
{
    public function index()
    {
        return Inertia::render('Sponsor/Packages/Index', [
            'packages' => SponsorshipPackage::with('activity')->latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'activity_id' => 'nullable|exists:events,id',
            'name' => 'required|string|max:100',
            'min_amount' => 'required|numeric|min:0',
            'benefits' => 'nullable|text',
            'quota' => 'nullable|integer|min:0',
            'seq_no' => 'nullable|integer',
        ]);
        SponsorshipPackage::create($validated);
        return redirect()->back()->with('success', 'Paket sponsor berhasil ditambahkan.');
    }

    public function update(Request $request, SponsorshipPackage $package)
    {
        $validated = $request->validate([
            'activity_id' => 'nullable|exists:events,id',
            'name' => 'required|string|max:100',
            'min_amount' => 'required|numeric|min:0',
            'benefits' => 'nullable|text',
            'quota' => 'nullable|integer|min:0',
            'seq_no' => 'nullable|integer',
        ]);
        $package->update($validated);
        return redirect()->back()->with('success', 'Paket sponsor berhasil diperbarui.');
    }

    public function destroy(SponsorshipPackage $package)
    {
        $package->delete();
        return redirect()->back()->with('success', 'Paket sponsor berhasil dihapus.');
    }
}
