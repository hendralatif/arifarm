<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoatBirth extends Model
{
    use HasFactory;

    protected $fillable = [
        'mother_id', 'father_id', 'birth_date',
        'total_kids', 'male_count', 'female_count', 'stillborn_count',
        'birth_condition', 'mother_condition', 'notes', 'recorded_by',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function mother()
    {
        return $this->belongsTo(Goat::class, 'mother_id');
    }

    public function father()
    {
        return $this->belongsTo(Goat::class, 'father_id');
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function getBirthConditionLabelAttribute(): string
    {
        return match ($this->birth_condition) {
            'normal'    => 'Normal',
            'assisted'  => 'Dibantu',
            'cesarean'  => 'Caesar',
            default     => ucfirst($this->birth_condition),
        };
    }
}
