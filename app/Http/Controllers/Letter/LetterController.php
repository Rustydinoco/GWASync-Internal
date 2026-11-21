<?php

namespace App\Http\Controllers\Letter;

use App\Http\Controllers\Controller;
use App\Models\Letter;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LetterController extends Controller
{
    public function index()
    {
        return Inertia::render('Letter/Index', [
            'letters' => Letter::with('creator')->latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'letter_number' => 'required|string|max:50|unique:letters,letter_number',
            'title' => 'required|string|max:200',
            'content' => 'nullable|text',
            'recipient' => 'nullable|string|max:150',
            'sender' => 'nullable|string|max:150',
            'letter_date' => 'required|date',
            'category' => 'nullable|string|max:100',
            'status' => 'required|in:Draft,Diajuan,Disetujui,Ditolak,Dikirim',
        ]);
        Letter::create($validated);
        return redirect()->back()->with('success', 'Surat berhasil ditambahkan.');
    }

    public function update(Request $request, Letter $letter)
    {
        $validated = $request->validate([
            'letter_number' => 'required|string|max:50|unique:letters,letter_number,' . $letter->id,
            'title' => 'required|string|max:200',
            'content' => 'nullable|text',
            'recipient' => 'nullable|string|max:150',
            'sender' => 'nullable|string|max:150',
            'letter_date' => 'required|date',
            'category' => 'nullable|string|max:100',
            'status' => 'required|in:Draft,Diajuan,Disetujui,Ditolak,Dikirim',
        ]);
        $letter->update($validated);
        return redirect()->back()->with('success', 'Surat berhasil diperbarui.');
    }

    public function destroy(Letter $letter)
    {
        $letter->delete();
        return redirect()->back()->with('success', 'Surat berhasil dihapus.');
    }
}
