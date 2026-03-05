<?php

namespace App\Filament\SilosPanel\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SilosStatsOverview extends StatsOverviewWidget
{
    protected static ?string $heading = 'Indicadores de Silos';

    protected static ?int $sort = 1;
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        return [
            Stat::make('Stock Total', '0 kg')
                ->description('Suma de stock en todos los silos')
                ->color('primary'),
            Stat::make('Kg Disponibles', '0 kg')
                ->description('Capacidad restante total')
                ->color('success'),
            Stat::make('Humedad Promedio', '0 %')
                ->description('Promedio ponderado')
                ->color('info'),
            Stat::make('Silos Llenos', '0')
                ->description('Silos al 100%')
                ->color('warning'),
            Stat::make('En Reparacion', '0')
                ->description('Silos fuera de servicio')
                ->color('danger'),
        ];
    }
}
