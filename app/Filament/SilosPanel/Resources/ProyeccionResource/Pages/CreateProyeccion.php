<?php

namespace App\Filament\SilosPanel\Resources\ProyeccionResource\Pages;

use App\Filament\SilosPanel\Resources\ProyeccionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProyeccion extends CreateRecord
{
    protected static string $resource = ProyeccionResource::class;

        public function getTitle(): string
    {
        return ' '; // Cambia este texto al título deseado

    }
    
}
