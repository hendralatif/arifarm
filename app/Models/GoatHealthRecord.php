<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoatHealthRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'goat_id', 'check_date', 'record_type', 'diagnosis',
        'treatment', 'medicine', 'medicine_dose', 'vet_name',
        'health_status', 'next_checkup', 'notes', 'recorded_by',
    ];

    protected $casts = [
        'check_date'   => 'date',
        'next_checkup' => 'date',
        'medicine_dose'=> 'decimal:2',
    ];

    public function goat()
    {
        return $this->belongsTo(Goat::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function getRecordTypeLabelAttribute(): string
    {
        return match ($this->record_type) {
            'checkup'     => 'Pemeriksaan Rutin',
            'vaccination' => 'Vaksinasi',
            'treatment'   => 'Pengobatan',
            'observation' => 'Observasi',
            default       => ucfirst($this->record_type),
        };
    }

    public function getHealthStatusLabelAttribute(): string
    {
        return match ($this->health_status) {
            'healthy'    => 'Sehat',
            'sick'       => 'Sakit',
            'recovering' => 'Dalam Pemulihan',
            'critical'   => 'Kritis',
            default      => ucfirst($this->health_status),
        };
    }

    public function getHealthStatusBadgeAttribute(): string
    {
        return match ($this->health_status) {
            'healthy'    => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            'sick'       => 'bg-rose-100 text-rose-700 border-rose-200',
            'recovering' => 'bg-amber-100 text-amber-700 border-amber-200',
            'critical'   => 'bg-red-200 text-red-800 border-red-300',
            default      => 'bg-slate-100 text-slate-600 border-slate-200',
        };
    }
}
