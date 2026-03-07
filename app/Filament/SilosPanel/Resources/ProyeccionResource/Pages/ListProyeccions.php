<?php

namespace App\Filament\SilosPanel\Resources\ProyeccionResource\Pages;

use App\Filament\SilosPanel\Resources\ProyeccionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProyeccions extends ListRecords
{
    protected static string $resource = ProyeccionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nueva Proyección'), // Cambia este texto al deseado
            
            Actions\Action::make('exportar_excel')
                ->label('Exportar Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    return $this->exportToExcel();
                }),
        ];
    }

        public function getTitle(): string
    {
        return ' '; // Cambia este texto al título deseado

    }

        public function getBreadcrumb(): string
    {
        return ' '; // Cambia este texto al breadcrumb deseado
    }

}
