<?php

namespace App\Filament\Resources\BarloventoIngresosResource\Pages;

use App\Filament\Resources\BarloventoIngresosResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBarloventoIngresos extends CreateRecord
{
    protected static string $resource = BarloventoIngresosResource::class;

    public function getTitle(): string
    {
        return 'Nuevo Ingreso de hacienda'; // Cambia este texto al título deseado

    }

    public function getBreadcrumb(): string
    {
        return 'Nuevo Ingreso de hacienda'; // Cambia este texto al breadcrumb deseado
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(), // Mantiene el botón "Crear"
            $this->getCancelFormAction(), // Mantiene el botón "Cancelar"
        ];
    }
}
