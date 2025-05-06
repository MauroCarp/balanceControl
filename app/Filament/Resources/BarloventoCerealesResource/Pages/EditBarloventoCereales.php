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
            Actions\ViewAction::make()
            ->label('Ver')
            ->icon('heroicon-o-eye'),
            Actions\DeleteAction::make()
            ->icon('heroicon-o-trash'),
        ];
    }
}
