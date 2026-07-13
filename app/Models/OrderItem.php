<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'goat_id',
        'quantity',
        'price_at_purchase'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function goat()
    {
        return $this->belongsTo(Goat::class);
    }

    // Accessor for price
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price_at_purchase, 0, ',', '.');
    }

    // Accessor for subtotal
    public function getFormattedSubtotalAttribute()
    {
        return 'Rp ' . number_format($this->price_at_purchase * $this->quantity, 0, ',', '.');
    }
}
