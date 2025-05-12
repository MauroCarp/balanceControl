<?php

namespace App\Filament\Resources\DestinosEgresosResource\Pages;

use App\Filament\Resources\DestinosEgresosResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDestinosEgresos extends ListRecords
{
    protected static string $resource = DestinosEgresosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
