<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConsignatariosResource\Pages;
use App\Filament\Resources\ConsignatariosResource\RelationManagers;
use App\Models\Consignatarios;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ConsignatariosResource extends Resource
{
    protected static ?string $model = Consignatarios::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-end-on-rectangle';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('porcentajeComision')
                    ->label('% Comisión')
                    ->required()
                    ->numeric()
                    ->maxLength(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                'Nombre' => Tables\Columns\TextColumn::make('nombre')
                    ->sortable()
                    ->searchable(),
                'Comisión' => Tables\Columns\TextColumn::make('porcentajeComision')
                    ->sortable()
                    ->searchable()
                    ->label('% Comisión'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(), // Configura la acción de edición para abrirse en un modal
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
            'index' => Pages\ListConsignatarios::route('/'),
            // 'create' => Pages\CreateConsignatarios::route('/create'),
            'edit' => Pages\EditConsignatarios::route('/{record}/edit'),
        ];
    }
}
