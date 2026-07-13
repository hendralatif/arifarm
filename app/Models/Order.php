<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'invoice_number',
        'total_amount',
        'shipping_address',
        'phone_number',
        'notes',
        'shipping_method',
        'shipping_cost',
        'status',
        'payment_method',
        'payment_receipt',
        'tracking_number',
        'snap_token',
        'shipping_distance',
        'is_wonosobo',
        'payment_type',
        'dp_amount'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Accessor for Formatted Total
    public function getFormattedTotalAttribute()
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    public function getSubtotalAttribute()
    {
        return $this->items->sum(function($item) {
            return $item->price_at_purchase * $item->quantity;
        });
    }

    public function getFormattedSubtotalAttribute()
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    public function getFormattedShippingCostAttribute()
    {
        if ($this->shipping_method === 'diambil') {
            return 'Rp 0 (Diambil Sendiri)';
        }
        
        // If outside range (> 200 km) and shipping_cost is 0 but not approved yet
        if ($this->shipping_distance > 200 && $this->shipping_cost == 0 && $this->status === 'pending_approval') {
            return 'Dihitung Manual (Di luar jangkauan)';
        }

        return $this->shipping_cost > 0 
            ? 'Rp ' . number_format($this->shipping_cost, 0, ',', '.') 
            : ($this->status === 'pending_approval' ? 'Dihitung Admin' : 'Rp 0 (Gratis)');
    }

    public function getRemainingBalanceAttribute()
    {
        return max(0, $this->total_amount - $this->dp_amount);
    }

    public function getFormattedDpAmountAttribute()
    {
        return 'Rp ' . number_format($this->dp_amount, 0, ',', '.');
    }

    public function getFormattedRemainingBalanceAttribute()
    {
        return 'Rp ' . number_format($this->remaining_balance, 0, ',', '.');
    }

    public static function calculateShippingCost($distance, $isWonosobo)
    {
        $cost = 0;
        $isOutsideRange = false;

        if ($distance >= 0 && $distance <= 25) {
            $cost = 0;
        } elseif ($distance >= 26 && $distance <= 45) {
            $cost = 200000;
        } elseif ($distance >= 46 && $distance <= 65) {
            $cost = 250000;
        } elseif ($distance >= 66 && $distance <= 85) {
            $cost = 300000;
        } elseif ($distance >= 86 && $distance <= 100) {
            $cost = 400000;
        } elseif ($distance >= 101 && $distance <= 200) {
            $cost = 500000;
        } else {
            $cost = 0;
            $isOutsideRange = true;
        }

        if ($isWonosobo && !$isOutsideRange) {
            $cost = $cost * 0.8; // 20% discount
        }

        return [
            'cost' => $cost,
            'outside_range' => $isOutsideRange
        ];
    }

    // Helper for badge colors
    public function getStatusBadgeClassAttribute()
    {
        return match ($this->status) {
            'pending_approval' => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-900/40 dark:text-slate-400 dark:border-slate-800',
            'pending_payment' => 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-900',
            'pending_verification' => 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-900',
            'processing' => 'bg-indigo-100 text-indigo-800 border-indigo-200 dark:bg-indigo-950/40 dark:text-indigo-300 dark:border-indigo-900',
            'shipped' => 'bg-purple-100 text-purple-800 border-purple-200 dark:bg-purple-950/40 dark:text-purple-300 dark:border-purple-900',
            'completed' => 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-900',
            'cancelled' => 'bg-rose-100 text-rose-800 border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-900',
            default => 'bg-slate-100 text-slate-800 border-slate-200 dark:bg-slate-950/40 dark:text-slate-300 dark:border-slate-900',
        };
    }

    // Helper for readable status labels in Indonesian
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending_approval' => 'Menunggu Persetujuan',
            'pending_payment' => 'Menunggu Pembayaran',
            'pending_verification' => 'Menunggu Verifikasi',
            'processing' => 'Sedang Diproses',
            'shipped' => 'Dalam Pengiriman',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => 'Unknown',
        };
    }
}
