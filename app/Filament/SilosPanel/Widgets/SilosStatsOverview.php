<?php

namespace App\Filament\SilosPanel\Widgets;

use App\Models\Silo;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SilosStatsOverview extends StatsOverviewWidget
{
    protected static ?string $heading = 'Indicadores de Silos';

    protected static ?int $sort = 1;
    protected static bool $isLazy = false;

    protected $listeners = ['silo-stock-actualizado' => '$refresh'];

    protected function getStats(): array
    {
        $stockTotal     = Silo::sum('stock_actual_kg');
        $capacidadTotal = Silo::sum('capacidad_kg');
        $humedadProm    = Silo::whereNotNull('humedad')->avg('humedad');
        $silosLlenos    = Silo::where('estado', 'lleno')->count();
        $enReparacion   = Silo::where('estado', 'en_reparacion')->count();

        return [
            Stat::make('Stock Total', number_format($stockTotal, 0, ',', '.') . ' kg')
                ->description('Suma de stock en todos los silos')
                ->color('primary'),
            Stat::make('Kg Disponibles', number_format($capacidadTotal - $stockTotal, 0, ',', '.') . ' kg')
                ->description('Capacidad libre total')
                ->color('primary'),
            Stat::make('Humedad Promedio', number_format($humedadProm ?? 0, 2, ',', '.') . ' %')
                // ->description('Promedio de silos con dato')
                ->color('primary'),
            Stat::make('Silos Llenos', $silosLlenos)
                ->description('Silos al 100%')
                ->color('primary'),
            Stat::make('En Reparación', $enReparacion)
                ->description('Silos fuera de servicio')
                ->color('danger'),
        ];
    }
}
