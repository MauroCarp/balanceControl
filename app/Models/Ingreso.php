<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Ingreso extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted(): void
    {
        static::created(function (Ingreso $ingreso) {
            Log::channel('daily')->info('[Ingreso] Registro creado', [
                'ingreso_id'   => $ingreso->id,
                'silo_destino' => $ingreso->silo_destino,
                'cantidad'     => $ingreso->cantidad,
            ]);

            $silo = Silo::find($ingreso->silo_destino);

            if ($silo) {
                $stockAntes = $silo->stock_actual_kg;
                $silo->increment('stock_actual_kg', $ingreso->cantidad);
                $silo->update(['estado' => 'activo']);
                $silo->update(['cereal' => $ingreso->cultivo]);
                $silo->refresh();

                Log::channel('daily')->info('[Ingreso] Stock incrementado', [
                    'silo_id'       => $silo->id,
                    'silo_nombre'   => $silo->nombre,
                    'stock_antes'   => $stockAntes,
                    'incremento'    => $ingreso->cantidad,
                    'stock_despues' => $silo->stock_actual_kg,
                ]);

            } else {
                Log::channel('daily')->warning('[Ingreso] Silo no encontrado', [
                    'silo_destino_buscado' => $ingreso->silo_destino,
                ]);
            }
        });

        static::deleted(function (Ingreso $ingreso) {
            Log::channel('daily')->info('[Ingreso] Registro eliminado', [
                'ingreso_id'   => $ingreso->id,
                'silo_destino' => $ingreso->silo_destino,
                'cantidad'     => $ingreso->cantidad,
            ]);

            $silo = Silo::find($ingreso->silo_destino);

            if ($silo) {
                $stockAntes = $silo->stock_actual_kg;
                $silo->decrement('stock_actual_kg', $ingreso->cantidad);
                $silo->refresh();

                Log::channel('daily')->info('[Ingreso] Stock decrementado', [
                    'silo_id'       => $silo->id,
                    'silo_nombre'   => $silo->nombre,
                    'stock_antes'   => $stockAntes,
                    'decremento'    => $ingreso->cantidad,
                    'stock_despues' => $silo->stock_actual_kg,
                ]);
            } else {
                Log::channel('daily')->warning('[Ingreso] Silo no encontrado al eliminar', [
                    'silo_destino_buscado' => $ingreso->silo_destino,
                ]);
            }
        });
    }
}
