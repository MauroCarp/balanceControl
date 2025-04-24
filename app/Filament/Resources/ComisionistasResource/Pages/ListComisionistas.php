<?php

namespace App\Filament\Resources\ComisionistasResource\Pages;

use App\Filament\Resources\ComisionistasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListComisionistas extends ListRecords
{
    protected static string $resource = ComisionistasResource::class;

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
            ->label('Nuevo Comisionista'), // Cambia este texto al deseado
        ];
    }

}
