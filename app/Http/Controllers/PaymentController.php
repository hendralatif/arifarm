<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct()
    {
        // Initialize Midtrans Configuration
        \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
        \Midtrans\Config::$clientKey = config('services.midtrans.client_key');
        \Midtrans\Config::$isProduction = config('services.midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('services.midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('services.midtrans.is_3ds');
    }

    /**
     * Generate or retrieve snap token for an order.
     */
    public function getSnapToken($id)
    {
        $order = Order::with('items.goat', 'user')->where('user_id', Auth::id())->findOrFail($id);

        if ($order->status !== 'pending_payment') {
            return response()->json(['error' => 'Pesanan tidak dalam status menunggu pembayaran.'], 400);
        }

        // If snap token already exists, reuse it
        if ($order->snap_token) {
            return response()->json(['snap_token' => $order->snap_token]);
        }

        // Check if server key is configured
        if (empty(config('services.midtrans.server_key'))) {
            return response()->json(['error' => 'Midtrans Server Key belum dikonfigurasi. Hubungi admin.'], 500);
        }

        try {
            $items = [];
            foreach ($order->items as $item) {
                $items[] = [
                    'id' => 'goat_' . $item->goat_id,
                    'price' => (int) $item->price_at_purchase,
                    'quantity' => (int) $item->quantity,
                    'name' => substr($item->goat->name, 0, 50),
                ];
            }

            // Add shipping cost as an item detail if > 0
            if ($order->shipping_cost > 0) {
                $items[] = [
                    'id' => 'shipping_cost',
                    'price' => (int) $order->shipping_cost,
                    'quantity' => 1,
                    'name' => 'Biaya Pengiriman (Ongkir)',
                ];
            }

            // Split first name and last name
            $fullName = $order->user->name;
            $nameParts = explode(' ', $fullName, 2);
            $firstName = $nameParts[0];
            $lastName = $nameParts[1] ?? '';

            // Construct Midtrans transaction parameters
            $params = [
                'transaction_details' => [
                    'order_id' => $order->invoice_number,
                    'gross_amount' => (int) $order->total_amount,
                ],
                'item_details' => $items,
                'customer_details' => [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $order->user->email,
                    'phone' => $order->phone_number,
                    'shipping_address' => [
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'phone' => $order->phone_number,
                        'address' => $order->shipping_address,
                    ]
                ],
                'expiry' => [
                    'start_time' => date('Y-m-d H:i:s O'),
                    'unit' => 'hours',
                    'duration' => 24
                ],
                // Mengaktifkan metode pembayaran Virtual Account secara spesifik
                'enabled_payments' => [
                    'bca_va',
                    'bni_va',
                    'bri_va',
                    'mandiri_va',
                    'permata_va',
                    'other_va'
                ]
            ];

            // Request snap token from Midtrans
            $snapToken = \Midtrans\Snap::getSnapToken($params);

            // Save snap token to database
            $order->update(['snap_token' => $snapToken]);

            return response()->json(['snap_token' => $snapToken]);

        } catch (\Exception $e) {
            Log::error('Gagal membuat Snap Token Midtrans: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menghubungkan ke payment gateway: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Handle webhook callback notifications from Midtrans.
     */
    public function handleNotification(Request $request)
    {
        $serverKey = config('services.midtrans.server_key');
        $orderId = $request->input('order_id');
        $statusCode = $request->input('status_code');
        $grossAmount = $request->input('gross_amount');
        $signatureKey = $request->input('signature_key');

        // Verify Signature Key
        $localSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        if ($localSignature !== $signatureKey) {
            Log::warning('Signature callback Midtrans tidak cocok untuk Order: ' . $orderId);
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $order = Order::with('items.goat')->where('invoice_number', $orderId)->first();
        if (!$order) {
            Log::warning('Order tidak ditemukan untuk callback Midtrans: ' . $orderId);
            return response()->json(['message' => 'Order not found'], 404);
        }

        $transactionStatus = $request->input('transaction_status');
        $type = $request->input('payment_type');
        $fraud = $request->input('fraud_status');

        Log::info("Webhook Midtrans: Order ID {$orderId}, Status {$transactionStatus}, Type {$type}");

        // Handle transaction status transition
        if ($transactionStatus === 'settlement' || ($transactionStatus === 'capture' && $fraud === 'accept')) {
            $order->update([
                'status' => 'processing',
                'payment_method' => 'midtrans_' . $type
            ]);
        } elseif ($transactionStatus === 'pending') {
            $order->update([
                'status' => 'pending_payment',
                'payment_method' => 'midtrans_' . $type
            ]);
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            if ($order->status !== 'cancelled') {
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
                $order->update([
                    'status' => 'cancelled',
                    'payment_method' => 'midtrans_' . $type
                ]);
            }
        }

        return response()->json(['message' => 'Callback processed successfully']);
    }

    /**
     * Handle user redirect after payment completion on Midtrans.
     */
    public function paymentFinish(Request $request)
    {
        $invoiceNumber = $request->query('order_id');
        
        if (!$invoiceNumber) {
            return redirect()->route('dashboard')->with('error', 'Pesanan tidak ditemukan.');
        }

        $order = Order::where('invoice_number', $invoiceNumber)->firstOrFail();

        // Query Midtrans status API to verify transaction (essential fallback for Localhost)
        try {
            if (empty(config('services.midtrans.server_key'))) {
                throw new \Exception('Server key empty');
            }

            $status = \Midtrans\Transaction::status($order->invoice_number);
            
            $transactionStatus = $status->transaction_status;
            $type = $status->payment_type;
            $fraud = $status->fraud_status ?? null;

            Log::info("Finish Halaman Midtrans: Order ID {$invoiceNumber}, Status {$transactionStatus}");

            if ($transactionStatus === 'settlement' || ($transactionStatus === 'capture' && $fraud === 'accept')) {
                $order->update([
                    'status' => 'processing',
                    'payment_method' => 'midtrans_' . $type
                ]);
                return redirect()->route('orders.show', $order->id)->with('success', 'Pembayaran Anda berhasil diverifikasi secara otomatis!');
            } elseif ($transactionStatus === 'pending') {
                $order->update([
                    'status' => 'pending_payment',
                    'payment_method' => 'midtrans_' . $type
                ]);
                return redirect()->route('orders.show', $order->id)->with('info', 'Pembayaran Anda sedang menunggu penyelesaian.');
            } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                if ($order->status !== 'cancelled') {
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
                    $order->update([
                        'status' => 'cancelled',
                        'payment_method' => 'midtrans_' . $type
                    ]);
                }
                return redirect()->route('orders.show', $order->id)->with('error', 'Pembayaran Anda gagal, kedaluwarsa, atau dibatalkan.');
            }
        } catch (\Exception $e) {
            Log::warning('Gagal memverifikasi status pembayaran dari Midtrans API: ' . $e->getMessage());
        }

        // Default redirect fallback
        return redirect()->route('orders.show', $order->id)->with('info', 'Kembali dari halaman pembayaran. Silakan periksa status pesanan Anda beberapa saat lagi.');
    }
}
