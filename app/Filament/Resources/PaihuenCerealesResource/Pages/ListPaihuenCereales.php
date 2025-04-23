<?php

namespace App\Filament\Resources\PaihuenCerealesResource\Pages;

use App\Filament\Resources\PaihuenCerealesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaihuenCereales extends ListRecords
{
    protected static string $resource = PaihuenCerealesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
