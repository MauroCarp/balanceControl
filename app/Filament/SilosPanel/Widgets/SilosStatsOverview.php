<?php

namespace App\Filament\SilosPanel\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class SilosStatsOverview extends StatsOverviewWidget
{
    protected static ?string $heading = 'Indicadores de Silos';

    protected function getCards(): array
    {
        return [
            Card::make('Stock Total', '0 kg')
                ->description('Suma de stock en todos los silos')
                ->color('primary')
                ->extraAttributes(['class' => 'text-lg']),
            Card::make('Kg Disponibles', '0 kg')
                ->description('Capacidad restante total')
                ->color('success')
                ->extraAttributes(['class' => 'text-lg']),
            Card::make('Humedad Promedio', '0 %')
                ->description('Promedio ponderado')
                ->color('info')
                ->extraAttributes(['class' => 'text-lg']),
            Card::make('Silos Llenos', '0')
                ->description('Silos al 100%')
                ->color('warning'),
            Card::make('En Reparacion', '0')
                ->description('Silos fuera de servicio')
                ->color('danger'),
        ];
    }
}
