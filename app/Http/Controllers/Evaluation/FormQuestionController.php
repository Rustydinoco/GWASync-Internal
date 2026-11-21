<?php

namespace App\Http\Controllers\Evaluation;

use App\Http\Controllers\Controller;
use App\Models\FormQuestion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FormQuestionController extends Controller
{
    public function index()
    {
        return Inertia::render('Evaluation/FormQuestions/Index', [
            'questions' => FormQuestion::with('form')->latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'form_id' => 'required|exists:forms,id',
            'question' => 'required|string|max:500',
            'question_type' => 'required|in:Teks,Pilihan,Ceklist,File',
            'options' => 'nullable|string',
            'is_required' => 'sometimes|in:0,1',
            'sort_order' => 'nullable|integer',
        ]);
        FormQuestion::create($validated);
        return redirect()->back()->with('success', 'Pertanyaan formulir berhasil ditambahkan.');
    }

    public function update(Request $request, FormQuestion $question)
    {
        $validated = $request->validate([
            'form_id' => 'required|exists:forms,id',
            'question' => 'required|string|max:500',
            'question_type' => 'required|in:Teks,Pilihan,Ceklist,File',
            'options' => 'nullable|string',
            'is_required' => 'sometimes|in:0,1',
            'sort_order' => 'nullable|integer',
        ]);
        $question->update($validated);
        return redirect()->back()->with('success', 'Pertanyaan formulir berhasil diperbarui.');
    }

    public function destroy(FormQuestion $question)
    {
        $question->delete();
        return redirect()->back()->with('success', 'Pertanyaan formulir berhasil dihapus.');
    }
}
