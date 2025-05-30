<?php

namespace App\Filament\Resources\PaihuenEgresoCerealesResource\Pages;

use App\Filament\Resources\PaihuenEgresoCerealesResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePaihuenEgresoCereales extends CreateRecord
{
    protected static string $resource = PaihuenEgresoCerealesResource::class;

     public function getTitle(): string
    {
        return 'Nuevo Egreso de Insumo'; // Cambia este texto al título deseado

    }

    public function getBreadcrumb(): string
    {
        return 'Nuevo Egreso de Insumo'; // Cambia este texto al breadcrumb deseado
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()->after(function () {
                $this->redirect($this->getResource()::getUrl('index'));
            }), // Mantiene el botón "Crear" y redirige a la lista después de crear
            $this->getCancelFormAction(), // Mantiene el botón "Cancelar"
        ];
    }
}
