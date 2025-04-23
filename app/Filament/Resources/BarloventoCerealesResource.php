<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BarloventoCerealesResource\Pages;
use App\Filament\Resources\BarloventoCerealesResource\RelationManagers;
use App\Models\BarloventoCereales;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BarloventoCerealesResource extends Resource
{
    protected static ?string $model = BarloventoCereales::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-end-on-rectangle';
    protected static ?string $navigationGroup = 'Barlovento'; // Agrupa en "Barlovento"
    protected static ?string $navigationLabel = 'Ingresos Cereal'; // Nombre del 
    protected static ?int $navigationSort = 3;

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
            'index' => Pages\ListBarloventoCereales::route('/'),
            'create' => Pages\CreateBarloventoCereales::route('/create'),
            'edit' => Pages\EditBarloventoCereales::route('/{record}/edit'),
        ];
    }
}
