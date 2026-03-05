<?php

namespace App\Filament\SilosPanel\Widgets;

use Filament\Widgets\Widget;

class DetalleSilosWidget extends Widget
{
    protected static string $view = 'filament.silos-panel.widgets.detalle-silos-widget';

    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 5;
    protected static bool $isLazy = false;

    protected function getViewData(): array
    {
        return [
            'rows' => [
                ['silo' => 'Silo 1', 'stock' => 60000, 'humedad' => 12.5, 'capacidad' => 100000, 'disponible' => 40000, 'estado' => 'Activo'],
                ['silo' => 'Silo 2', 'stock' => 110000, 'humedad' => 11.2, 'capacidad' => 120000, 'disponible' => 10000, 'estado' => 'Lleno'],
                ['silo' => 'Silo 3', 'stock' => 30000, 'humedad' => 13.0, 'capacidad' => 80000, 'disponible' => 50000, 'estado' => 'Por llenarse'],
                ['silo' => 'Silo 4', 'stock' => 0, 'humedad' => 0, 'capacidad' => 90000, 'disponible' => 90000, 'estado' => 'En reparacion'],
            ],
        ];
    }
}
