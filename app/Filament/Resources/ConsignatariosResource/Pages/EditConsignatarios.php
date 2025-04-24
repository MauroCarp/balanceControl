<?php

namespace App\Filament\Resources\ConsignatariosResource\Pages;

use App\Filament\Resources\ConsignatariosResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConsignatarios extends EditRecord
{
    protected static string $resource = ConsignatariosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
