<?php

namespace App\Http\Controllers\Evaluation;

use App\Http\Controllers\Controller;
use App\Models\Form;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FormController extends Controller
{
    public function index()
    {
        return Inertia::render('Evaluation/Forms/Index', [
            'forms' => Form::latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|text',
            'is_active' => 'sometimes|in:0,1',
        ]);
        Form::create($validated);
        return redirect()->back()->with('success', 'Formulir berhasil ditambahkan.');
    }

    public function update(Request $request, Form $form)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|text',
            'is_active' => 'sometimes|in:0,1',
        ]);
        $form->update($validated);
        return redirect()->back()->with('success', 'Formulir berhasil diperbarui.');
    }

    public function destroy(Form $form)
    {
        $form->delete();
        return redirect()->back()->with('success', 'Formulir berhasil dihapus.');
    }
}
