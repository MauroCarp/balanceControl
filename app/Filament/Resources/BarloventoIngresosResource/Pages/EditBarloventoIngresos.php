<?php

namespace App\Filament\Resources\BarloventoIngresosResource\Pages;

use App\Filament\Resources\BarloventoIngresosResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBarloventoIngresos extends EditRecord
{
    protected static string $resource = BarloventoIngresosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
