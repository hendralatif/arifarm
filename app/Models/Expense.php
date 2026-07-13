<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_date',
        'category',
        'title',
        'amount',
        'description',
        'recorded_by',
        'goat_id'
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2'
    ];

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function goat()
    {
        return $this->belongsTo(Goat::class);
    }

    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getCategoryLabelAttribute()
    {
        return match ($this->category) {
            'pakan' => 'Pakan',
            'kesehatan' => 'Kesehatan',
            'operasional' => 'Operasional',
            'pembelian_hewan' => 'Pembelian Hewan',
            'lainnya' => 'Lainnya',
            default => ucfirst($this->category)
        };
    }

    public function getCategoryBadgeClassAttribute()
    {
        return match ($this->category) {
            'pakan' => 'bg-emerald-50 text-emerald-700 border-emerald-250',
            'kesehatan' => 'bg-blue-50 text-blue-700 border-blue-250',
            'operasional' => 'bg-amber-50 text-amber-700 border-amber-250',
            'pembelian_hewan' => 'bg-indigo-50 text-indigo-700 border-indigo-250',
            default => 'bg-slate-50 text-slate-700 border-slate-250'
        };
    }
}
