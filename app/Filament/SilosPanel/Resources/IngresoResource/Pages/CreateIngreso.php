<?php

namespace App\Filament\SilosPanel\Resources\IngresoResource\Pages;

use App\Filament\SilosPanel\Resources\IngresoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateIngreso extends CreateRecord
{
    protected static string $resource = IngresoResource::class;

    public function getTitle(): string
    {
        return ' ';
    }

    public function getBreadcrumb(): string
    {
        return 'Nuevo Ingreso';
    }
}
