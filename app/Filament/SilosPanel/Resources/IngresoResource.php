<?php

namespace App\Filament\SilosPanel\Resources;

use App\Filament\SilosPanel\Resources\IngresoResource\Pages;
use App\Models\Ingreso;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class IngresoResource extends Resource
{
    protected static ?string $model = Ingreso::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\DatePicker::make('fecha')
                ->required(),
            Forms\Components\Select::make('silo_destino')
                ->label('Silo destino')
                ->options([
                    'silo_1' => 'Silo 1',
                    'silo_2' => 'Silo 2',
                ])
                ->required()
                ->searchable(),
            Forms\Components\TextInput::make('proveedor')
                ->required(),
            Forms\Components\TextInput::make('cantidad')
                ->numeric()
                ->required(),
            Forms\Components\TextInput::make('humedad')
                ->numeric()
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fecha')->date(),
                Tables\Columns\TextColumn::make('silo_destino')->label('Silo destino'),
                Tables\Columns\TextColumn::make('proveedor'),
                Tables\Columns\TextColumn::make('cantidad'),
                Tables\Columns\TextColumn::make('humedad'),
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
            'index' => Pages\ListIngresos::route('/'),
            'create' => Pages\CreateIngreso::route('/create'),
        ];
    }
}
