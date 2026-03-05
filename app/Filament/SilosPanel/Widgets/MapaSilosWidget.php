<?php

namespace App\Filament\SilosPanel\Widgets;

use Filament\Widgets\Widget;

class MapaSilosWidget extends Widget
{
    protected static string $view = 'filament.silos-panel.widgets.mapa-silos-widget';

    protected function getViewData(): array
    {
        return [
            'silos' => [
                ['nombre' => 'Silo 1', 'capacidad' => 100, 'disponible' => 40, 'cultivo' => 'Maiz', 'estado' => 'activo'],
                ['nombre' => 'Silo 2', 'capacidad' => 120, 'disponible' => 10, 'cultivo' => 'Soja', 'estado' => 'lleno'],
                ['nombre' => 'Silo 3', 'capacidad' => 80, 'disponible' => 50, 'cultivo' => 'Maiz', 'estado' => 'por_llenarse'],
                ['nombre' => 'Silo 4', 'capacidad' => 90, 'disponible' => 90, 'cultivo' => 'Soja', 'estado' => 'reparacion'],
            ],
        ];
    }
}
