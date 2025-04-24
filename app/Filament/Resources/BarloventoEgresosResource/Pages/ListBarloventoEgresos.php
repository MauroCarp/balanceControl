<?php

namespace App\Filament\Resources\BarloventoEgresosResource\Pages;

use App\Filament\Resources\BarloventoEgresosResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBarloventoEgresos extends ListRecords
{
    protected static string $resource = BarloventoEgresosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Registrar Nuevo Egreso'), // Cambia este texto al deseado
        ];
    }

    public function getTitle(): string
    {
        return 'Egresos de Hacienda'; // Cambia este texto al título deseado

    }

    public function getBreadcrumb(): string
    {
        return 'Egresos de Hacienda'; // Cambia este texto al breadcrumb deseado
    }
    
}
