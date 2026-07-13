<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'stock_kg',
        'description'
    ];

    protected $casts = [
        'stock_kg' => 'decimal:2'
    ];

    public function feedings()
    {
        return $this->hasMany(GoatFeeding::class);
    }
}
