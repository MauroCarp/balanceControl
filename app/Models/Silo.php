<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Silo extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'capacidad_kg'   => 'integer',
        'stock_actual_kg' => 'integer',
        'humedad'        => 'float',
    ];

    protected static function booted(): void
    {
        static::saving(function (Silo $silo) {
            if ($silo->capacidad_kg > 0 && $silo->stock_actual_kg >= $silo->capacidad_kg) {
                $silo->estado = 'lleno';
            }
        });
    }

    public function getPorcentajeOcupacionAttribute(): float
    {
        if ($this->capacidad_kg === 0) {
            return 0;
        }

        return round(($this->stock_actual_kg / $this->capacidad_kg) * 100, 2);
    }

    public function getKgDisponiblesAttribute(): int
    {
        return max(0, $this->capacidad_kg - $this->stock_actual_kg);
    }
}
