<?php

namespace App\Filament\Resources\ConsignatariosResource\Pages;

use App\Filament\Resources\ConsignatariosResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConsignatarios extends ListRecords
{
    protected static string $resource = ConsignatariosResource::class;

    protected function getTableActions(): array
    {
        return [
            Actions\EditAction::make()->modal(), // Configura la acción de edición para abrirse en un modal
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Nuevo Consignatario'), // Cambia este texto al deseado
        ];
    }
}
