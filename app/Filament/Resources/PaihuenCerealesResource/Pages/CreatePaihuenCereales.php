<?php

namespace App\Filament\Resources\PaihuenCerealesResource\Pages;

use App\Filament\Resources\PaihuenCerealesResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePaihuenCereales extends CreateRecord
{
    protected static string $resource = PaihuenCerealesResource::class;

    public function getTitle(): string
    {
        return 'Nuevo Ingreso de Cereal'; // Cambia este texto al título deseado

    }

    public function getBreadcrumb(): string
    {
        return 'Nuevo Ingreso de Cereal'; // Cambia este texto al breadcrumb deseado
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
