<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminPaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user', 'items.goat')
            ->whereIn('status', ['pending_payment', 'pending_verification', 'processing', 'completed']);

        // Filter by payment status group
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by invoice or customer name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$search}%"));
            });
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->latest()->paginate(15)->withQueryString();

        // Summary stats
        $totalIncome = Order::where('status', 'completed')->sum('total_amount');
        $pendingVerification = Order::where('status', 'pending_verification')->count();
        $pendingPayment = Order::where('status', 'pending_payment')->count();
        $thisMonthIncome = Order::where('status', 'completed')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->sum('total_amount');

        return view('admin.payments.index', compact(
            'payments', 'totalIncome', 'pendingVerification', 'pendingPayment', 'thisMonthIncome'
        ));
    }

    public function verify(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'action' => 'required|in:approve,reject',
        ]);

        if ($request->action === 'approve') {
            $order->status = 'processing';
            $order->save();
            return redirect()->route('admin.payments.index')
                ->with('success', "Pembayaran invoice {$order->invoice_number} berhasil diverifikasi! Status berubah ke Diproses.");
        } else {
            // Reject — delete receipt, revert to pending_payment
            if ($order->payment_receipt) {
                $fullPath = public_path($order->payment_receipt);
                if (file_exists($fullPath) && !str_starts_with($order->payment_receipt, 'http')) {
                    @unlink($fullPath);
                }
                $order->payment_receipt = null;
            }
            $order->status = 'pending_payment';
            $order->save();
            return redirect()->route('admin.payments.index')
                ->with('warning', "Pembayaran invoice {$order->invoice_number} ditolak. Pelanggan harus upload ulang bukti pembayaran.");
        }
    }
}
