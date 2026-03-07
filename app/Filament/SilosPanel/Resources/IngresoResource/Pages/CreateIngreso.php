<?php

namespace App\Filament\SilosPanel\Resources\IngresoResource\Pages;

use App\Filament\SilosPanel\Resources\IngresoResource;
use App\Models\Silo;
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
        return 'Nuevo Ingreso'; // Cambia este texto al breadcrumb deseado
    }

    protected function afterCreate(): void
    {
        $silo = Silo::find($this->record->silo_destino);

        if ($silo) {
            $silo->increment('stock_actual_kg', $this->record->cantidad);
        }
    }
}
