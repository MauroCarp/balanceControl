<?php

namespace App\Filament\Resources\BarloventoIngresosResource\Pages;

use App\Filament\Resources\BarloventoIngresosResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;

class ViewBarloventoIngresos extends ViewRecord
{
    protected static string $resource = BarloventoIngresosResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            // Puedes agregar widgets aquí si es necesario
        ];
    }

}