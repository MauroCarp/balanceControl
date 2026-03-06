<?php

namespace App\Filament\SilosPanel\Resources\IngresoResource\Pages;

use App\Filament\SilosPanel\Resources\IngresoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIngresos extends ListRecords
{
    protected static string $resource = IngresoResource::class;

        public function getTitle(): string
    {
        return ' '; // Cambia este texto al título deseado

    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
