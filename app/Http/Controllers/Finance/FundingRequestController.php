<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\FundingRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FundingRequestController extends Controller
{
    public function index()
    {
        return Inertia::render('Finance/FundingRequests/Index', [
            'requests' => FundingRequest::with('organization', 'program', 'activity')->latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'reason' => 'required|string',
            'total_estimate' => 'required|numeric|min:0',
            'total_actual' => 'nullable|numeric|min:0',
            'needed_by_date' => 'nullable|date',
            'payment_method' => 'required|in:Tunai,Transfer,Pengajuan Dana',
            'status' => 'required|in:Draft,Diajuan,Disetujui,Ditolak,Selesai',
        ]);
        FundingRequest::create($validated);
        return redirect()->back()->with('success', 'Pengajuan dana berhasil ditambahkan.');
    }

    public function update(Request $request, FundingRequest $requestFunding)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'reason' => 'required|string',
            'total_estimate' => 'required|numeric|min:0',
            'total_actual' => 'nullable|numeric|min:0',
            'needed_by_date' => 'nullable|date',
            'payment_method' => 'required|in:Tunai,Transfer,Pengajuan Dana',
            'status' => 'required|in:Draft,Diajuan,Disetujui,Ditolak,Selesai',
        ]);
        $requestFunding->update($validated);
        return redirect()->back()->with('success', 'Pengajuan dana berhasil diperbarui.');
    }

    public function destroy(FundingRequest $requestFunding)
    {
        $requestFunding->delete();
        return redirect()->back()->with('success', 'Pengajuan dana berhasil dihapus.');
    }
}
