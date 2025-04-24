<?php

namespace App\Filament\Resources\BarloventoCerealesResource\Pages;

use App\Filament\Resources\BarloventoCerealesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBarloventoCereales extends ListRecords
{
    protected static string $resource = BarloventoCerealesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Registrar Nuevo Ingreso de Cereal'), // Cambia este texto al deseado
        ];
    }

    public function getTitle(): string
    {
        return 'Ingresos de Cereal'; // Cambia este texto al título deseado

    }

    public function getBreadcrumb(): string
    {
        return 'Ingresos de Cereal'; // Cambia este texto al breadcrumb deseado
    }
}
