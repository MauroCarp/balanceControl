<?php

namespace App\Filament\Resources\PaihuenCerealesResource\Pages;

use App\Filament\Resources\PaihuenCerealesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaihuenCereales extends ListRecords
{
    protected static string $resource = PaihuenCerealesResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    public function getTitle(): string
    {
        return ' '; // Cambia este texto al título deseado

    }

    public function getBreadcrumb(): string
    {
        return 'Ingresos de Insumo'; // Cambia este texto al breadcrumb deseado
    }
}
