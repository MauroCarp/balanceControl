<?php

namespace App\Filament\Resources\BarloventoIngresosResource\Pages;

use App\Filament\Resources\BarloventoIngresosResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBarloventoIngresos extends ListRecords
{
    protected static string $resource = BarloventoIngresosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Registrar Nuevo Ingreso'), // Cambia este texto al deseado
        ];
    }

    public function getTitle(): string
    {
        return 'Ingresos de Hacienda'; // Cambia este texto al título deseado

    }

    public function getBreadcrumb(): string
    {
        return 'Ingresos de Hacienda'; // Cambia este texto al breadcrumb deseado
    }
}
