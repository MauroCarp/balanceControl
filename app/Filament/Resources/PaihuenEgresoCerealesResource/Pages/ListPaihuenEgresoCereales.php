<?php

namespace App\Filament\Resources\PaihuenEgresoCerealesResource\Pages;

use App\Filament\Resources\PaihuenEgresoCerealesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaihuenEgresoCereales extends ListRecords
{
    protected static string $resource = PaihuenEgresoCerealesResource::class;

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
        return 'Egresos de Insumo'; // Cambia este texto al breadcrumb deseado
    }
}
