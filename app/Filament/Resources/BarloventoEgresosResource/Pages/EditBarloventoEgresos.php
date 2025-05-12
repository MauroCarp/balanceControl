<?php

namespace App\Filament\Resources\BarloventoEgresosResource\Pages;

use App\Filament\Resources\BarloventoEgresosResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBarloventoEgresos extends EditRecord
{
    protected static string $resource = BarloventoEgresosResource::class;

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
