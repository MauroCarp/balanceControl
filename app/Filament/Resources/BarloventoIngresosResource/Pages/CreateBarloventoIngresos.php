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
        return ' '; // Cambia este texto al título deseado

    }

    public function getBreadcrumb(): string
    {
        return 'Ingreso de Hacienda Origen'; // Cambia este texto al breadcrumb deseado
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()->label('Guardar'), // Cambia el texto del botón a "Guardar"

        ];
    }
}
