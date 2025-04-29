<?php

namespace App\Filament\Resources\PaihuenCerealesResource\Pages;

use App\Filament\Resources\PaihuenCerealesResource;
use App\Models\PaihuenCereales;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;

class ViewPaihuenCereales extends ViewRecord
{
    protected static string $resource = PaihuenCerealesResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            // Puedes agregar widgets aquí si es necesario
        ];
    }

    public function getTitle(): string
    {
        return 'Detalle Ingreso Cereales'; // Cambia este texto al título deseado

    }

    public function getBreadcrumb(): string
    {
        return 'Ver detalle'; // Cambia este texto al breadcrumb deseado
    }


}