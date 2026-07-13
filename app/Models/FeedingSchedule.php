<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedingSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'day_of_week',
        'session',
        'feed_stock_1_id',
        'feed_stock_2_id',
        'quantity_1_kg',
        'quantity_2_kg',
        'qty_type_1',
        'qty_type_2',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'quantity_1_kg' => 'decimal:2',
        'quantity_2_kg' => 'decimal:2'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function feedStock1()
    {
        return $this->belongsTo(FeedStock::class, 'feed_stock_1_id');
    }

    public function feedStock2()
    {
        return $this->belongsTo(FeedStock::class, 'feed_stock_2_id');
    }

    /**
     * Get estimated total kg based on goat count for a given pakan slot.
     * @param string $slot '1' or '2'
     * @param int $goatCount current total goats
     */
    public function getEstimatedKg(string $slot, int $goatCount): float
    {
        $qty  = $slot === '1' ? (float)$this->quantity_1_kg : (float)$this->quantity_2_kg;
        $type = $slot === '1' ? $this->qty_type_1 : $this->qty_type_2;
        return $type === 'per_goat' ? $qty * $goatCount : $qty;
    }

    public function getQtyTypeLabelAttribute(): string
    {
        // convenience: check if either slot is per_goat
        if ($this->qty_type_1 === 'per_goat' || $this->qty_type_2 === 'per_goat') {
            return 'Per Ekor';
        }
        return 'Tetap';
    }

    public function getDayLabelAttribute()
    {
        return match ($this->day_of_week) {
            'senin' => 'Senin',
            'selasa' => 'Selasa',
            'rabu' => 'Rabu',
            'kamis' => 'Kamis',
            'jumat' => 'Jumat',
            'sabtu' => 'Sabtu',
            'minggu' => 'Minggu',
            default => ucfirst($this->day_of_week)
        };
    }

    public function getSessionLabelAttribute()
    {
        return match ($this->session) {
            'pagi' => 'Pagi',
            'sore' => 'Sore',
            default => ucfirst($this->session)
        };
    }
}
