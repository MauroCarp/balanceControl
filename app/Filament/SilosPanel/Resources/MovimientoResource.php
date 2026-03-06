<?php

namespace App\Filament\SilosPanel\Resources;

use App\Filament\SilosPanel\Resources\MovimientoResource\Pages;
use App\Models\Movimiento;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MovimientoResource extends Resource
{
    protected static ?string $model = Movimiento::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-up-down';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\DatePicker::make('fecha')
                ->required(),
            Forms\Components\Select::make('silo_origen')
                ->label('Silo origen')
                ->options([
                    'silo_1' => 'Silo 1',
                    'silo_2' => 'Silo 2',
                ])
                ->required()
                ->searchable(),
            Forms\Components\TextInput::make('cantidad')
                ->numeric()
                ->required(),
            Forms\Components\Select::make('destino')
                ->options([
                    'planta' => 'Planta',
                    'puerto' => 'Puerto',
                ])
                ->required()
                ->searchable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fecha')->date(),
                Tables\Columns\TextColumn::make('silo_origen')->label('Silo origen'),
                Tables\Columns\TextColumn::make('cantidad'),
                Tables\Columns\TextColumn::make('destino'),
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
            'index' => Pages\ListMovimientos::route('/'),
            'create' => Pages\CreateMovimiento::route('/create'),
        ];
    }
}
