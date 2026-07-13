<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goat extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'seller_id',
        'name',
        'slug',
        'description',
        'price',
        'purchase_price',
        'stock',
        'weight_kg',
        'age_months',
        'gender',
        'breed',
        'health_status',
        'vaccine_status',
        'images',
        'status',
        'acquisition_type'
    ];

    protected $casts = [
        'images' => 'array',
        'vaccine_status' => 'boolean',
        'price' => 'decimal:2',
        'purchase_price' => 'decimal:2',
        'weight_kg' => 'decimal:2'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function expense()
    {
        return $this->hasOne(Expense::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function weighings()
    {
        return $this->hasMany(GoatWeighing::class)->orderBy('weighed_at', 'asc');
    }

    // Accessor for Formatted Price
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    // Accessor for first image
    public function getFirstImageAttribute()
    {
        if (is_array($this->images) && count($this->images) > 0) {
            return $this->images[0];
        }
        return 'https://images.unsplash.com/photo-1608755728617-aefab37d2edd?w=600&auto=format&fit=crop';
    }

    public function getAcquisitionTypeLabelAttribute(): string
    {
        return match ($this->acquisition_type) {
            'beli' => 'Pembelian',
            'kelahiran' => 'Kelahiran',
            'lainnya' => 'Lainnya',
            default => ucfirst($this->acquisition_type)
        };
    }

    public function getAcquisitionTypeBadgeAttribute(): string
    {
        return match ($this->acquisition_type) {
            'beli' => 'bg-blue-50 text-blue-700 border-blue-200',
            'kelahiran' => 'bg-pink-50 text-pink-700 border-pink-200',
            default => 'bg-slate-50 text-slate-650 border-slate-200'
        };
    }
}
