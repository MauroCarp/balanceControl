<?php

namespace App\Filament\Resources\BarloventoCerealesResource\Pages;

use App\Filament\Resources\BarloventoCerealesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBarloventoCereales extends ListRecords
{
    protected static string $resource = BarloventoCerealesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
