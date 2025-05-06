<?php

namespace App\Filament\Resources\UsersResource\Pages;

use App\Filament\Resources\UsersResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UsersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Nuevo Usuario'), // Cambia este texto al deseado
            
        ];
    }

    public function getTitle(): string
    {
        return 'Usuarios'; // Cambia este texto al título deseado

    }

    public function getBreadcrumb(): string
    {
        return 'Usuarios'; // Cambia este texto al breadcrumb deseado
    }
}
