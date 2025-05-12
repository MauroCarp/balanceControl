<?php

namespace App\Filament\Resources\PaihuenCerealesResource\Pages;

use App\Filament\Resources\PaihuenCerealesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaihuenCereales extends EditRecord
{
    protected static string $resource = PaihuenCerealesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
            ->label('Ver')
            ->icon('heroicon-o-eye'),
            Actions\DeleteAction::make()
            ->icon('heroicon-o-trash'),
        ];
    }
}
