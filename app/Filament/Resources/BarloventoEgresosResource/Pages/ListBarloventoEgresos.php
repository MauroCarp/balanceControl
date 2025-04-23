<?php

namespace App\Filament\Resources\BarloventoEgresosResource\Pages;

use App\Filament\Resources\BarloventoEgresosResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBarloventoEgresos extends ListRecords
{
    protected static string $resource = BarloventoEgresosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
