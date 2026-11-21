<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinancialAccount;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FinancialAccountController extends Controller
{
    public function index()
    {
        return Inertia::render('Finance/Accounts/Index', [
            'accounts' => FinancialAccount::latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'account_type' => 'required|in:Kas,Bank',
            'bank_name' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:50',
            'account_holder' => 'nullable|string|max:150',
            'opening_balance' => 'nullable|numeric|min:0',
            'is_active' => 'sometimes|in:0,1',
        ]);
        FinancialAccount::create($validated);
        return redirect()->back()->with('success', 'Rekening keuangan berhasil ditambahkan.');
    }

    public function update(Request $request, FinancialAccount $account)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'account_type' => 'required|in:Kas,Bank',
            'bank_name' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:50',
            'account_holder' => 'nullable|string|max:150',
            'opening_balance' => 'nullable|numeric|min:0',
            'is_active' => 'sometimes|in:0,1',
        ]);
        $account->update($validated);
        return redirect()->back()->with('success', 'Rekening keuangan berhasil diperbarui.');
    }

    public function destroy(FinancialAccount $account)
    {
        $account->delete();
        return redirect()->back()->with('success', 'Rekening keuangan berhasil dihapus.');
    }
}
