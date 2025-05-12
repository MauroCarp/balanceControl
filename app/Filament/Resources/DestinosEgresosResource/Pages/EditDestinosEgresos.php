<?php

namespace App\Filament\Resources\DestinosEgresosResource\Pages;

use App\Filament\Resources\DestinosEgresosResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDestinosEgresos extends EditRecord
{
    protected static string $resource = DestinosEgresosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
