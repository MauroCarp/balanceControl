<?php

namespace App\Filament\SilosPanel\Resources\MovimientoResource\Pages;

use App\Filament\SilosPanel\Resources\MovimientoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMovimientos extends ListRecords
{
    protected static string $resource = MovimientoResource::class;

        public function getTitle(): string
    {
        return ' '; // Cambia este texto al título deseado

    }
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nuevo Movimiento'), // Cambia este texto al deseado
            
            Actions\Action::make('exportar_excel')
                ->label('Exportar Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    return $this->exportToExcel();
                }),
        ];
    }
}
