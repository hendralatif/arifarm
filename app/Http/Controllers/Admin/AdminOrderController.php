<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->latest()->paginate(10)->withQueryString();
        return view('admin.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with('user', 'items.goat.category')->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function verifyPayment(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $request->validate([
            'action' => 'required|in:approve,reject',
            'reason' => 'nullable|string|max:255',
        ]);

        if ($request->action === 'approve') {
            $order->status = 'processing';
            $order->save();
            return redirect()->route('admin.orders.show', $order->id)->with('success', 'Pembayaran berhasil disetujui! Status pesanan berubah menjadi Diproses.');
        } else {
            $order->status = 'pending_payment';
            
            // Delete the bad receipt so user can upload a new one
            if ($order->payment_receipt) {
                $fullPath = public_path($order->payment_receipt);
                if (file_exists($fullPath) && !str_starts_with($order->payment_receipt, 'http')) {
                    unlink($fullPath);
                }
                $order->payment_receipt = null;
            }
            
            $order->save();
            return redirect()->route('admin.orders.show', $order->id)->with('warning', 'Pembayaran ditolak. Bukti pembayaran telah dihapus agar pelanggan dapat mengunggah bukti baru.');
        }
    }

    public function approveOrder(Request $request, $id)
    {
        $order = Order::with('items.goat')->findOrFail($id);

        $request->validate([
            'shipping_cost' => 'required|numeric|min:0',
        ]);

        $subtotal = 0;
        foreach ($order->items as $item) {
            $subtotal += $item->price_at_purchase * $item->quantity;
        }

        $shippingCost = $order->shipping_method === 'diambil' ? 0 : $request->shipping_cost;

        $order->shipping_cost = $shippingCost;
        $order->total_amount = $subtotal + $shippingCost;
        
        // Recalculate DP if applicable
        if ($order->payment_type === 'dp') {
            $order->dp_amount = $order->total_amount * 0.30;
        } else {
            $order->dp_amount = 0;
        }

        $order->status = 'pending_payment';
        $order->save();

        return redirect()->route('admin.orders.show', $order->id)->with('success', 'Transaksi berhasil disetujui! Status pesanan berubah menjadi Menunggu Pembayaran dengan total yang disesuaikan.');
    }

    public function rejectOrder($id)
    {
        $order = Order::findOrFail($id);

        if ($order->status !== 'cancelled') {
            foreach ($order->items as $item) {
                $goat = $item->goat;
                $goat->stock += $item->quantity;
                if ($goat->status === 'sold') {
                    $goat->status = 'available';
                }
                $goat->save();
            }
            $order->status = 'cancelled';
            $order->save();
        }

        return redirect()->route('admin.orders.show', $order->id)->with('warning', 'Transaksi telah ditolak dan dibatalkan.');
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'status' => 'required|in:processing,shipped,completed,cancelled',
            'tracking_number' => 'nullable|string|max:255',
        ]);

        $oldStatus = $order->status;
        $order->status = $request->status;
        if ($request->filled('tracking_number')) {
            $order->tracking_number = $request->tracking_number;
        }
        
        // If cancelled, restore goat stocks
        if ($request->status === 'cancelled' && $oldStatus !== 'cancelled') {
            foreach ($order->items as $item) {
                $goat = $item->goat;
                $goat->stock += $item->quantity;
                if ($goat->status === 'sold') {
                    $goat->status = 'available';
                }
                $goat->save();
            }
        }

        $order->save();

        return redirect()->route('admin.orders.show', $order->id)->with('success', 'Status pesanan berhasil diperbarui.');
    }
}
