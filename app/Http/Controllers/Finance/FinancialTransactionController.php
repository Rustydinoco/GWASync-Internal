<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinancialTransaction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FinancialTransactionController extends Controller
{
    public function index()
    {
        return Inertia::render('Finance/Transactions/Index', [
            'transactions' => FinancialTransaction::with('account', 'category')->latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:financial_accounts,id',
            'category_id' => 'required|exists:financial_categories,id',
            'transaction_number' => 'required|string|max:50|unique:financial_transactions,transaction_number',
            'transaction_type' => 'required|in:Pemasukan,Pengeluaran',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string|max:255',
            'status' => 'required|in:Pending,Disetujui,Ditolak',
        ]);
        FinancialTransaction::create($validated);
        return redirect()->back()->with('success', 'Transaksi keuangan berhasil ditambahkan.');
    }

    public function update(Request $request, FinancialTransaction $transaction)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:financial_accounts,id',
            'category_id' => 'required|exists:financial_categories,id',
            'transaction_number' => 'required|string|max:50|unique:financial_transactions,transaction_number,' . $transaction->id,
            'transaction_type' => 'required|in:Pemasukan,Pengeluaran',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string|max:255',
            'status' => 'required|in:Pending,Disetujui,Ditolak',
        ]);
        $transaction->update($validated);
        return redirect()->back()->with('success', 'Transaksi keuangan berhasil diperbarui.');
    }

    public function destroy(FinancialTransaction $transaction)
    {
        $transaction->delete();
        return redirect()->back()->with('success', 'Transaksi keuangan berhasil dihapus.');
    }
}
