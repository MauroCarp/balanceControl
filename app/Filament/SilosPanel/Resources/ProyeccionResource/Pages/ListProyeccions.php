<?php

namespace App\Filament\SilosPanel\Resources\ProyeccionResource\Pages;

use App\Filament\SilosPanel\Resources\ProyeccionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProyeccions extends ListRecords
{
    protected static string $resource = ProyeccionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

        public function getTitle(): string
    {
        return ' '; // Cambia este texto al título deseado

    }
}
