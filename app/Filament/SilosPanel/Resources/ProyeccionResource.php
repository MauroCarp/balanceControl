<?php

namespace App\Filament\SilosPanel\Resources;

use App\Filament\SilosPanel\Resources\ProyeccionResource\Pages;
use App\Models\Proyeccion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProyeccionResource extends Resource
{
    protected static ?string $model = Proyeccion::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Radio::make('cultivo')
                ->options([
                    'maiz' => 'Maiz',
                    'soja' => 'Soja',
                ])
                ->inline()
                ->required(),
            Forms\Components\TextInput::make('consumo_diario_prom')
                ->label('Consumo diario prom')
                ->numeric()
                ->required(),
            Forms\Components\TextInput::make('kg_pendientes_ingresar')
                ->label('Kg pendientes ingresar')
                ->numeric()
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cultivo'),
                Tables\Columns\TextColumn::make('consumo_diario_prom')->label('Consumo diario prom'),
                Tables\Columns\TextColumn::make('kg_pendientes_ingresar')->label('Kg pendientes ingresar'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProyeccions::route('/'),
            'create' => Pages\CreateProyeccion::route('/create'),
        ];
    }
}
