<?php

namespace App\Filament\Resources\BarloventoCerealesResource\Pages;

use App\Filament\Resources\BarloventoCerealesResource;
use App\Models\PaihuenCereales;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\TextEntry;
use Filament\Actions\EditAction;

use Filament\Infolists\Components\Section;

class ViewBarloventoCereales extends ViewRecord
{
    protected static string $resource = BarloventoCerealesResource::class;

    
    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
            ->icon('heroicon-o-pencil-square'),
        ];
    }


    protected function getHeaderWidgets(): array
    {
        return [
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