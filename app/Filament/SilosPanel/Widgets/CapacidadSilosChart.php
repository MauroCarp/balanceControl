<?php

namespace App\Filament\SilosPanel\Widgets;

use Filament\Widgets\ChartWidget;

class CapacidadSilosChart extends ChartWidget
{
    protected static ?string $heading = 'Capacidad de Silos';

    protected int|string|array $columnSpan = 'half';
    protected static ?int $sort = 2;
    protected static bool $isLazy = false;

    protected $listeners = ['silo-stock-actualizado' => '$refresh'];

    protected function getData(): array
    {
        $silos = \App\Models\Silo::orderBy('nombre')->get();

        return [
            'datasets' => [
                [
                    'label'           => 'Capacidad máxima (tn)',
                    'data'            => $silos->map(fn ($s) => round($s->capacidad_kg / 1000, 1))->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.6)',
                ],
                [
                    'label'           => 'Stock actual (tn)',
                    'data'            => $silos->map(fn ($s) => round($s->stock_actual_kg / 1000, 1))->toArray(),
                    'backgroundColor' => 'rgba(16, 185, 129, 0.6)',
                ],
            ],
            'labels' => $silos->pluck('nombre')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
