<?php

namespace App\Filament\SilosPanel\Widgets;

use App\Models\Silo;
use Filament\Widgets\Widget;

class DetalleSilosWidget extends Widget
{
    protected static string $view = 'filament.silos-panel.widgets.detalle-silos-widget';

    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 5;
    protected static bool $isLazy = false;

    protected $listeners = ['silo-stock-actualizado' => '$refresh'];

    protected function getViewData(): array
    {
        $silos = Silo::orderBy('nombre')->get();

        return [
            'rows' => $silos->map(fn (Silo $s) => [
                'silo'      => $s->nombre,
                'cereal'    => $s->cereal ?? '—',
                'stock'     => $s->stock_actual_kg,
                'humedad'   => $s->humedad ?? 0,
                'capacidad' => $s->capacidad_kg,
                'disponible'=> $s->kg_disponibles,
                'estado'    => match ($s->estado) {
                    'activo'        => 'Activo',
                    'vacio'         => 'Vacío',
                    'lleno'         => 'Lleno',
                    'en_reparacion' => 'En reparación',
                    default         => ucfirst($s->estado),
                },
            ])->toArray(),
        ];
    }
}
