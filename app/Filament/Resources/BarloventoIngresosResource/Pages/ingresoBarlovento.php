<?php

namespace App\Filament\Resources\BarloventoIngresosResource\Pages;

use App\Filament\Resources\BarloventoIngresosResource;
use Filament\Resources\Pages\Page;

class ingresoBarlovento extends Page
{
    protected static string $resource = BarloventoIngresosResource::class;

    protected static string $view = 'filament.resources.barlovento-ingresos-resource.pages.ingreso-barlovento';

    protected static ?string $title = 'Custom Page Title';

    public static function getPages(): array
{
    return [
        // 'index' => ListBarloventoIngresos::route('/'),
        'ingreso-barlovento' => IngresoBarlovento::route('/ingreso-barlovento'),
    ];
}
}
