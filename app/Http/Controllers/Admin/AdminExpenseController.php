<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with('recorder');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        $expenses = $query->latest('expense_date')->paginate(10)->withQueryString();

        // Calculate summary stats
        $totalExpense = Expense::sum('amount');
        $thisMonthExpense = Expense::whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');
        $todayExpense = Expense::whereDate('expense_date', today())->sum('amount');

        return view('admin.expenses.index', compact('expenses', 'totalExpense', 'thisMonthExpense', 'todayExpense'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'expense_date' => 'required|date',
            'category'     => 'required|in:pakan,kesehatan,operasional,pembelian_hewan,lainnya',
            'title'        => 'required|string|max:255',
            'amount'       => 'required|numeric|min:1',
            'description'  => 'nullable|string|max:550',
        ], [
            'expense_date.required' => 'Tanggal pengeluaran wajib diisi.',
            'category.required'     => 'Kategori pengeluaran wajib dipilih.',
            'title.required'        => 'Nama pengeluaran wajib diisi.',
            'amount.required'       => 'Nominal pengeluaran wajib diisi.',
        ]);

        Expense::create([
            'expense_date' => $request->expense_date,
            'category'     => $request->category,
            'title'        => $request->title,
            'amount'       => $request->amount,
            'description'  => $request->description,
            'recorded_by'  => Auth::id(),
        ]);

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Catatan pengeluaran berhasil disimpan.');
    }

    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Catatan pengeluaran berhasil dihapus.');
    }
}
