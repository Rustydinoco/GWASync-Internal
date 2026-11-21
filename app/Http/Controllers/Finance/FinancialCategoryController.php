<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinancialCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FinancialCategoryController extends Controller
{
    public function index()
    {
        return Inertia::render('Finance/Categories/Index', [
            'categories' => FinancialCategory::latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'category_type' => 'required|in:Pemasukan,Pengeluaran',
            'is_active' => 'sometimes|in:0,1',
        ]);
        FinancialCategory::create($validated);
        return redirect()->back()->with('success', 'Kategori keuangan berhasil ditambahkan.');
    }

    public function update(Request $request, FinancialCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'category_type' => 'required|in:Pemasukan,Pengeluaran',
            'is_active' => 'sometimes|in:0,1',
        ]);
        $category->update($validated);
        return redirect()->back()->with('success', 'Kategori keuangan berhasil diperbarui.');
    }

    public function destroy(FinancialCategory $category)
    {
        $category->delete();
        return redirect()->back()->with('success', 'Kategori keuangan berhasil dihapus.');
    }
}
