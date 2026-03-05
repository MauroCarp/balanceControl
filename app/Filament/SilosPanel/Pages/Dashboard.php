<?php

namespace App\Filament\SilosPanel\Pages;

use App\Filament\SilosPanel\Widgets\AjusteStockCeroWidget;
use App\Filament\SilosPanel\Widgets\CapacidadSilosChart;
use App\Filament\SilosPanel\Widgets\DetalleSilosWidget;
use App\Filament\SilosPanel\Widgets\MapaSilosWidget;
use App\Filament\SilosPanel\Widgets\SilosStatsOverview;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'inicio';
    protected static ?string $title = 'Gestión dinámica de Silos y Consumo';

    public function getWidgets(): array
    {
        return [
            MapaSilosWidget::class,
            SilosStatsOverview::class,
            CapacidadSilosChart::class,
            AjusteStockCeroWidget::class,
            DetalleSilosWidget::class,
        ];
    }

}
