<?php

namespace App\Filament\Resources\PaihuenCerealesResource\Pages;

use App\Filament\Resources\PaihuenCerealesResource;
use App\Models\PaihuenCereales;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Actions\EditAction;

use Filament\Pages\Actions;


class ViewPaihuenCereales extends ViewRecord
{
    protected static string $resource = PaihuenCerealesResource::class;


    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
            ->icon('heroicon-o-pencil-square'),
        ];
    }


    public function getTitle(): string
    {
        return 'Detalle Ingreso Insumos'; // Cambia este texto al título deseado

    }

    public function getBreadcrumb(): string
    {
        return 'Ver detalle'; // Cambia este texto al breadcrumb deseado
    }


}