<?php

namespace App\Http\Controllers;

use App\Models\Goat;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'shipping_method' => 'required|in:diambil,diantar',
            'shipping_address' => 'required_if:shipping_method,diantar|nullable|string',
            'phone_number' => 'required|string',
            'notes' => 'nullable|string',
            'shipping_distance' => 'required_if:shipping_method,diantar|nullable|integer|min:0',
            'is_wonosobo' => 'nullable|boolean',
            'payment_type' => 'required|in:full,dp',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('catalog')->with('error', 'Keranjang belanja Anda kosong.');
        }

        // DB Transaction
        DB::beginTransaction();

        try {
            // Generate Invoice Number: INV-YYYYMMDD-RAND
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(5));

            $shippingAddress = $request->shipping_method === 'diambil' 
                ? 'Diambil langsung ke kandang ARI FARM (Jl. Peternak Raya No. 42, Purwokerto Selatan)' 
                : $request->shipping_address;

            // Calculate shipping cost draft
            $shippingCost = 0;
            $distance = $request->shipping_method === 'diantar' ? (int)$request->shipping_distance : null;
            $isWonosobo = $request->shipping_method === 'diantar' ? (bool)$request->is_wonosobo : false;

            if ($request->shipping_method === 'diantar' && !is_null($distance)) {
                $shippingCalc = Order::calculateShippingCost($distance, $isWonosobo);
                $shippingCost = $shippingCalc['cost'];
            }

            // Create Order skeleton
            $order = Order::create([
                'user_id' => Auth::id(),
                'invoice_number' => $invoiceNumber,
                'total_amount' => 0, // Will update below
                'shipping_address' => $shippingAddress,
                'phone_number' => $request->phone_number,
                'notes' => $request->notes,
                'shipping_method' => $request->shipping_method,
                'shipping_cost' => $shippingCost,
                'shipping_distance' => $distance,
                'is_wonosobo' => $isWonosobo,
                'payment_type' => $request->payment_type,
                'dp_amount' => 0, // Will update below
                'status' => 'pending_approval',
                'payment_method' => 'bank_transfer',
            ]);

            $subtotal = 0;
            foreach ($cart as $id => $item) {
                $goat = Goat::findOrFail($id);

                // Re-verify availability
                if ($goat->status !== 'available' || $goat->stock < $item['quantity']) {
                    throw new \Exception("Kambing '{$goat->name}' tidak tersedia lagi dalam jumlah tersebut.");
                }

                // Create Order Item
                OrderItem::create([
                    'order_id' => $order->id,
                    'goat_id' => $goat->id,
                    'quantity' => $item['quantity'],
                    'price_at_purchase' => $item['price']
                ]);

                // Reduce stock
                $goat->stock -= $item['quantity'];
                if ($goat->stock <= 0) {
                    $goat->status = 'sold';
                }
                $goat->save();

                $subtotal += $item['price'] * $item['quantity'];
            }

            // Recalculate totals
            $totalAmount = $subtotal + $shippingCost;
            $dpAmount = 0;
            if ($request->payment_type === 'dp') {
                $dpAmount = $totalAmount * 0.30; // 30% Down Payment
            }

            // Update order final totals
            $order->total_amount = $totalAmount;
            $order->dp_amount = $dpAmount;
            $order->save();

            DB::commit();

            // Clear session cart
            session()->forget('cart');

            return redirect()->route('orders.show', $order->id)->with('success', 'Pesanan Anda berhasil dibuat! Mohon tunggu persetujuan admin.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('cart.index')->with('error', 'Gagal memproses pesanan: ' . $e->getMessage());
        }
    }

    public function dashboard()
    {
        $orders = Order::where('user_id', Auth::id())->latest()->get();
        return view('dashboard', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with('items.goat.category', 'user')->where('user_id', Auth::id())->findOrFail($id);
        return view('orders.show', compact('order'));
    }

    public function uploadReceipt(Request $request, $id)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        if ($order->status !== 'pending_payment') {
            return redirect()->back()->with('error', 'Status pesanan saat ini tidak memperbolehkan unggah bukti pembayaran.');
        }

        $request->validate([
            'payment_receipt' => 'required|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        if ($request->hasFile('payment_receipt')) {
            $imageName = 'receipt_' . $order->invoice_number . '_' . time() . '.' . $request->payment_receipt->extension();
            
            // Save to public storage
            $request->payment_receipt->move(public_path('uploads/receipts'), $imageName);

            $order->payment_receipt = 'uploads/receipts/' . $imageName;
            $order->status = 'pending_verification';
            $order->save();

            return redirect()->route('orders.show', $order->id)->with('success', 'Bukti pembayaran berhasil diunggah! Mohon tunggu konfirmasi admin.');
        }

        return redirect()->back()->with('error', 'Gagal mengunggah gambar bukti.');
    }

    public function invoice($id)
    {
        $order = Order::with('items.goat', 'user')->where('user_id', Auth::id())->findOrFail($id);
        return view('orders.invoice', compact('order'));
    }

    public function cancel($id)
    {
        $order = Order::with('items.goat')->where('user_id', Auth::id())->findOrFail($id);

        // Only allow cancellation on these statuses
        $cancellableStatuses = ['pending_approval', 'pending_payment'];

        if (!in_array($order->status, $cancellableStatuses)) {
            return redirect()->route('orders.show', $order->id)
                ->with('error', 'Pesanan ini tidak dapat dibatalkan karena sudah dalam proses lebih lanjut. Hubungi admin untuk bantuan.');
        }

        // Restore goat stock
        foreach ($order->items as $item) {
            $goat = $item->goat;
            if ($goat) {
                $goat->stock += $item->quantity;
                if ($goat->status === 'sold') {
                    $goat->status = 'available';
                }
                $goat->save();
            }
        }

        // Remove uploaded receipt if exists (for pending_payment state)
        if ($order->payment_receipt) {
            $fullPath = public_path($order->payment_receipt);
            if (file_exists($fullPath) && !str_starts_with($order->payment_receipt, 'http')) {
                unlink($fullPath);
            }
            $order->payment_receipt = null;
        }

        $order->status = 'cancelled';
        $order->save();

        return redirect()->route('orders.show', $order->id)
            ->with('warning', 'Pesanan #' . $order->invoice_number . ' berhasil dibatalkan. Stok kambing telah dikembalikan.');
    }

    public function payments()
    {
        $orders = Order::where('user_id', Auth::id())
            ->whereIn('status', ['pending_approval', 'pending_payment', 'pending_verification'])
            ->latest()
            ->get();
        return view('orders.pembayaran', compact('orders'));
    }

    public function history(Request $request)
    {
        $status = $request->input('status');
        $query = Order::where('user_id', Auth::id());
        
        if ($status === 'active') {
            $query->whereIn('status', ['pending_approval', 'pending_payment', 'pending_verification', 'processing', 'shipped']);
        } elseif ($status === 'completed') {
            $query->where('status', 'completed');
        } elseif ($status === 'cancelled') {
            $query->where('status', 'cancelled');
        }
        
        $orders = $query->latest()->get();
        return view('orders.histori', compact('orders', 'status'));
    }

    public function confirmArrival($id)
    {
        $order = Order::where('user_id', \Illuminate\Support\Facades\Auth::id())->findOrFail($id);

        if ($order->status !== 'shipped') {
            return redirect()->back()->with('error', 'Status pesanan saat ini tidak dapat dikonfirmasi telah sampai.');
        }

        $order->status = 'completed';
        $order->save();

        return redirect()->route('orders.show', $order->id)->with('success', 'Terima kasih! Anda telah mengonfirmasi bahwa kambing sudah sampai.');
    }
}
