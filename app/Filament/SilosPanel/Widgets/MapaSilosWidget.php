<?php

namespace App\Filament\SilosPanel\Widgets;

use App\Models\Silo;
use Filament\Widgets\Widget;

class MapaSilosWidget extends Widget
{
    protected static string $view = 'filament.silos-panel.widgets.mapa-silos-widget';

    protected int|string|array $columnSpan = 'full';
    protected static bool $isLazy = false;

    protected function getViewData(): array
    {
        $silos = Silo::orderBy('nombre')->get();

        return [
            'silos' => $silos->map(fn (Silo $s) => [
                'nombre'    => $s->nombre,
                'capacidad' => round($s->capacidad_kg / 1000, 1),
                'disponible'=> round($s->kg_disponibles / 1000, 1),
                'cultivo'   => $s->cereal ?? 'Sin dato',
                'estado'    => $s->estado,
            ])->toArray(),
        ];
    }
}
