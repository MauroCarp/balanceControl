<?php

namespace App\Filament\SilosPanel\Widgets;

use Filament\Widgets\ChartWidget;

class CapacidadSilosChart extends ChartWidget
{
    protected static ?string $heading = 'Capacidad de Silos';

    protected function getData(): array
    {
        $labels = ['Silo 1', 'Silo 2', 'Silo 3'];

        $maximos = [100, 120, 80];
        $actuales = [60, 90, 40];

        return [
            'datasets' => [
                [
                    'label' => 'Capacidad maxima (tn)',
                    'data' => $maximos,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.6)',
                ],
                [
                    'label' => 'Stock actual (tn)',
                    'data' => $actuales,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.6)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
