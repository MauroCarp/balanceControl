<?php

namespace App\Filament\Resources\BarloventoCerealesResource\Pages;

use App\Filament\Resources\BarloventoCerealesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBarloventoCereales extends EditRecord
{
    protected static string $resource = BarloventoCerealesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
