<?php

namespace App\Filament\Resources\BarloventoEgresosResource\Pages;

use App\Filament\Resources\BarloventoEgresosResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBarloventoEgresos extends CreateRecord
{
    protected static string $resource = BarloventoEgresosResource::class;

    public function getTitle(): string
    {
        return 'Nuevo Egreso de hacienda'; // Cambia este texto al título deseado

    }

    public function getBreadcrumb(): string
    {
        return 'Nuevo Egreso de hacienda'; // Cambia este texto al breadcrumb deseado
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(), // Mantiene el botón "Crear"
            $this->getCancelFormAction(), // Mantiene el botón "Cancelar"
        ];
    }
}
