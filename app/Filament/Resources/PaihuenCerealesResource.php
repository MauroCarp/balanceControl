<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaihuenCerealesResource\Pages;
use App\Filament\Resources\PaihuenCerealesResource\RelationManagers;
use App\Models\PaihuenCereales;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaihuenCerealesResource extends Resource
{
    protected static ?string $model = PaihuenCereales::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-start-on-rectangle';
    protected static ?string $navigationGroup = 'Paihuen'; // Agrupa en "Barlovento"
    protected static ?string $navigationLabel = 'Ingresos Cereales'; // Nombre del 
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
            'index' => Pages\ListPaihuenCereales::route('/'),
            'create' => Pages\CreatePaihuenCereales::route('/create'),
            'edit' => Pages\EditPaihuenCereales::route('/{record}/edit'),
        ];
    }
}
