<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoatWeighing extends Model
{
    use HasFactory;

    protected $fillable = [
        'goat_id',
        'weight_kg',
        'weighed_at',
        'notes'
    ];

    protected $casts = [
        'weighed_at' => 'date',
        'weight_kg' => 'decimal:2'
    ];

    public function goat()
    {
        return $this->belongsTo(Goat::class);
    }
}
