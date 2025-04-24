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
            $this->getCreateFormAction(), // Mantiene el botón "Crear"
            // Actions\Action::make('crearYContinuar') // Nombre del botón
            // ->label('Crear y Continuar') // Texto del botón
            // ->action(function () {
            //     $this->form->save(); // Guarda el registro actual
            //     return redirect()->route('filament.resources.barlovento-ingresos-resource.edit'); // Redirige a otro formulario
            // })
            // ->color('success') // Color del botón (opcional)
            // ->icon('heroicon-o-arrow-right'), // Ícono del botón (opcional)
            // $this->getCancelFormAction(), // Mantiene el botón "Cancelar"

        ];
    }
}
