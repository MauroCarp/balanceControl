<?php

namespace App\Filament\Resources\ComisionistasResource\Pages;

use App\Filament\Resources\ComisionistasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditComisionistas extends EditRecord
{
    protected static string $resource = ComisionistasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
