<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BarloventoEgresosResource\Pages;
use App\Filament\Resources\BarloventoEgresosResource\RelationManagers;
use App\Models\BarloventoEgresos;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BarloventoEgresosResource extends Resource
{
    protected static ?string $model = BarloventoEgresos::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-start-on-rectangle';
    protected static ?string $navigationGroup = 'Barlovento'; // Agrupa en "Barlovento"
    protected static ?string $navigationLabel = 'Egresos Animales'; // Nombre del 
    protected static ?int $navigationSort = 2;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBarloventoEgresos::route('/'),
            'create' => Pages\CreateBarloventoEgresos::route('/create'),
            'edit' => Pages\EditBarloventoEgresos::route('/{record}/edit'),
        ];
    }
}
