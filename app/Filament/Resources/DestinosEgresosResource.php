<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DestinosEgresosResource\Pages;
use App\Filament\Resources\DestinosEgresosResource\RelationManagers;
use App\Models\DestinosEgresos;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DestinosEgresosResource extends Resource
{
    protected static ?string $model = DestinosEgresos::class;
    
    protected static bool $shouldRegisterNavigation = false;

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
            'index' => Pages\ListDestinosEgresos::route('/'),
            'create' => Pages\CreateDestinosEgresos::route('/create'),
            'edit' => Pages\EditDestinosEgresos::route('/{record}/edit'),
        ];
    }
}
