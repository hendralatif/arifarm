<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoatFeeding extends Model
{
    use HasFactory;

    protected $fillable = [
        'feeding_date', 'feeding_time',
        'feed_stock_1_id', 'feed_stock_2_id',
        'feed_type_1', 'feed_type_2',
        'quantity_1_kg', 'quantity_2_kg',
        'goat_count', 'session', 'notes', 'recorded_by',
    ];

    protected $casts = [
        'feeding_date'  => 'date',
        'quantity_1_kg' => 'decimal:2',
        'quantity_2_kg' => 'decimal:2',
    ];

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function feedStock1()
    {
        return $this->belongsTo(FeedStock::class, 'feed_stock_1_id');
    }

    public function feedStock2()
    {
        return $this->belongsTo(FeedStock::class, 'feed_stock_2_id');
    }

    public function getSessionLabelAttribute(): string
    {
        return match ($this->session) {
            'pagi'  => 'Pagi',
            'sore'  => 'Sore',
            default => ucfirst($this->session),
        };
    }
}
