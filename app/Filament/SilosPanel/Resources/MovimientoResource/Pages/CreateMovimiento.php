<?php

namespace App\Filament\SilosPanel\Resources\MovimientoResource\Pages;

use App\Filament\SilosPanel\Resources\MovimientoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMovimiento extends CreateRecord
{
    protected static string $resource = MovimientoResource::class;

        public function getTitle(): string
    {
        return ' '; // Cambia este texto al título deseado

    }

        public function getBreadcrumb(): string
    {
        return 'Nuevo Movimiento'; // Cambia este texto al breadcrumb deseado
    }

}
