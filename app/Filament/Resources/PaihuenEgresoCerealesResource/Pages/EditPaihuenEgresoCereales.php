<?php

namespace App\Filament\Resources\PaihuenEgresoCerealesResource\Pages;

use App\Filament\Resources\PaihuenEgresoCerealesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaihuenEgresoCereales extends EditRecord
{
    protected static string $resource = PaihuenEgresoCerealesResource::class;

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
