<?php

namespace App\Filament\SilosPanel\Widgets;

use App\Models\Silo;
use Filament\Widgets\Widget;

class MapaSilosWidget extends Widget
{
    protected static string $view = 'filament.silos-panel.widgets.mapa-silos-widget';

    protected int|string|array $columnSpan = 'full';
    protected static bool $isLazy = false;

    protected $listeners = ['silo-stock-actualizado' => '$refresh'];

    protected function getViewData(): array
    {
        $silos = Silo::orderBy('nombre','asc')->get();

        return [
            'silos' => $silos->map(fn (Silo $s) => [
                'nombre'    => $s->nombre,
                'capacidad' => $s->capacidad_kg,
                'disponible'=> $s->kg_disponibles,
                'cultivo'   => $s->cereal ?? 'Sin dato',
                'estado'    => $s->estado,
            ])->toArray(),
        ];
    }
}
