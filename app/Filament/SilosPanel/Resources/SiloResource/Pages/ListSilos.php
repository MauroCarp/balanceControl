<?php

namespace App\Filament\SilosPanel\Resources\SiloResource\Pages;

use App\Filament\SilosPanel\Resources\SiloResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSilos extends ListRecords
{
    protected static string $resource = SiloResource::class;

    public function getTitle(): string
    {
        return 'Silos';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth('2xl')
                ->label('Nuevo Silo'),
        ];
    }
}
