<?php

namespace App\Http\Controllers\Letter;

use App\Http\Controllers\Controller;
use App\Models\LetterDisposition;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LetterDispositionController extends Controller
{
    public function index()
    {
        return Inertia::render('Letter/Dispositions/Index', [
            'dispositions' => LetterDisposition::with('letter', 'fromUser', 'toMember')->latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'letter_id' => 'required|exists:letters,id',
            'from_user_id' => 'required|exists:users,id',
            'to_member_id' => 'required|exists:users,id',
            'instruction' => 'required|string|max:500',
            'due_date' => 'nullable|date',
            'status' => 'required|in:Belum,Selesai',
            'done_at' => 'nullable|date',
        ]);
        LetterDisposition::create($validated);
        return redirect()->back()->with('success', 'Disposisi surat berhasil ditambahkan.');
    }

    public function update(Request $request, LetterDisposition $disposition)
    {
        $validated = $request->validate([
            'letter_id' => 'required|exists:letters,id',
            'from_user_id' => 'required|exists:users,id',
            'to_member_id' => 'required|exists:users,id',
            'instruction' => 'required|string|max:500',
            'due_date' => 'nullable|date',
            'status' => 'required|in:Belum,Selesai',
            'done_at' => 'nullable|date',
        ]);
        $disposition->update($validated);
        return redirect()->back()->with('success', 'Disposisi surat berhasil diperbarui.');
    }

    public function destroy(LetterDisposition $disposition)
    {
        $disposition->delete();
        return redirect()->back()->with('success', 'Disposisi surat berhasil dihapus.');
    }
}
