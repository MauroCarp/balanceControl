<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Movimiento extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted(): void
    {
        static::created(function (Movimiento $movimiento) {
            Log::channel('daily')->info('[Movimiento] Registro creado', [
                'movimiento_id' => $movimiento->id,
                'silo_origen'   => $movimiento->silo_origen,
                'cantidad'      => $movimiento->cantidad,
            ]);

            $silo = Silo::find($movimiento->silo_origen);

            if ($silo) {
                $stockAntes = $silo->stock_actual_kg;
                $silo->decrement('stock_actual_kg', $movimiento->cantidad);
                $silo->refresh();

                Log::channel('daily')->info('[Movimiento] Stock decrementado', [
                    'silo_id'       => $silo->id,
                    'silo_nombre'   => $silo->nombre,
                    'stock_antes'   => $stockAntes,
                    'decremento'    => $movimiento->cantidad,
                    'stock_despues' => $silo->stock_actual_kg,
                ]);
            } else {
                Log::channel('daily')->warning('[Movimiento] Silo no encontrado', [
                    'silo_origen_buscado' => $movimiento->silo_origen,
                ]);
            }
        });

        static::deleted(function (Movimiento $movimiento) {
            Log::channel('daily')->info('[Movimiento] Registro eliminado', [
                'movimiento_id' => $movimiento->id,
                'silo_origen'   => $movimiento->silo_origen,
                'cantidad'      => $movimiento->cantidad,
            ]);

            $silo = Silo::find($movimiento->silo_origen);

            if ($silo) {
                $stockAntes = $silo->stock_actual_kg;
                $silo->increment('stock_actual_kg', $movimiento->cantidad);
                $silo->refresh();

                Log::channel('daily')->info('[Movimiento] Stock restaurado', [
                    'silo_id'       => $silo->id,
                    'silo_nombre'   => $silo->nombre,
                    'stock_antes'   => $stockAntes,
                    'incremento'    => $movimiento->cantidad,
                    'stock_despues' => $silo->stock_actual_kg,
                ]);
            } else {
                Log::channel('daily')->warning('[Movimiento] Silo no encontrado al eliminar', [
                    'silo_origen_buscado' => $movimiento->silo_origen,
                ]);
            }
        });
    }
}
