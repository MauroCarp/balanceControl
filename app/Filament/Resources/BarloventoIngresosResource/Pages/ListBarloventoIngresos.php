<?php

namespace App\Filament\Resources\BarloventoIngresosResource\Pages;

use App\Filament\Resources\BarloventoIngresosResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBarloventoIngresos extends ListRecords
{
    protected static string $resource = BarloventoIngresosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
