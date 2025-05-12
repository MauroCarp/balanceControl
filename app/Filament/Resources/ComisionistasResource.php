<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ComisionistasResource\Pages;
use App\Filament\Resources\ComisionistasResource\RelationManagers;
use App\Models\Comisionistas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ComisionistasResource extends Resource
{
    protected static ?string $model = Comisionistas::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

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
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected static bool $shouldRegisterNavigation = false;

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComisionistas::route('/'),
            // 'create' => Pages\CreateComisionistas::route('/create'),
            'edit' => Pages\EditComisionistas::route('/{record}/edit'),
        ];
    }
}
