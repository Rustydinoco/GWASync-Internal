<?php

namespace App\Http\Controllers\Evaluation;

use App\Http\Controllers\Controller;
use App\Models\FormResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FormResponseController extends Controller
{
    public function index()
    {
        return Inertia::render('Evaluation/FormResponses/Index', [
            'responses' => FormResponse::with('form', 'member')->latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'form_id' => 'required|exists:forms,id',
            'member_id' => 'nullable|exists:users,id',
            'respondent_name' => 'nullable|string|max:150',
            'respondent_email' => 'nullable|string|max:150',
            'answers' => 'required|array',
        ]);
        FormResponse::create($validated);
        return redirect()->back()->with('success', 'Respons formulir berhasil ditambahkan.');
    }

    public function update(Request $request, FormResponse $response)
    {
        $validated = $request->validate([
            'form_id' => 'required|exists:forms,id',
            'member_id' => 'nullable|exists:users,id',
            'respondent_name' => 'nullable|string|max:150',
            'respondent_email' => 'nullable|string|max:150',
            'answers' => 'required|array',
        ]);
        $response->update($validated);
        return redirect()->back()->with('success', 'Respons formulir berhasil diperbarui.');
    }

    public function destroy(FormResponse $response)
    {
        $response->delete();
        return redirect()->back()->with('success', 'Respons formulir berhasil dihapus.');
    }
}
