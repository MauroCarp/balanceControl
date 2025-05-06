<?php

namespace App\Filament\Resources\BarloventoIngresosResource\Pages;

use App\Filament\Resources\BarloventoIngresosResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Actions;

use Filament\Infolists\Components\Section;

class ViewBarloventoIngresos extends ViewRecord
{
    protected static string $resource = BarloventoIngresosResource::class;

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
        return 'Detalle Hacienda'; // Cambia este texto al título deseado

    }

    public function getBreadcrumb(): string
    {
        return 'Ver detalle'; // Cambia este texto al breadcrumb deseado
    }


}