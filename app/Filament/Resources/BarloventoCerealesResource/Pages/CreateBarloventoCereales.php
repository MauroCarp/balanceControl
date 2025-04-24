<?php

namespace App\Filament\Resources\BarloventoCerealesResource\Pages;

use App\Filament\Resources\BarloventoCerealesResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBarloventoCereales extends CreateRecord
{
    protected static string $resource = BarloventoCerealesResource::class;

    public function getTitle(): string
    {
        return 'Nuevo Ingreso de Cereal'; // Cambia este texto al título deseado

    }

    public function getBreadcrumb(): string
    {
        return 'Nuevo Ingreso de Cereal'; // Cambia este texto al breadcrumb deseado
    }

}
