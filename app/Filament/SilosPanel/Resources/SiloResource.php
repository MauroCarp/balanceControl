<?php

namespace App\Filament\SilosPanel\Resources;

use App\Filament\SilosPanel\Resources\SiloResource\Pages;
use App\Models\Silo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SiloResource extends Resource
{
    protected static ?string $model = Silo::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationLabel = 'Silos';

    protected static ?string $modelLabel = 'Silo';

    protected static ?string $pluralModelLabel = 'Silos';

    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('nombre')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('codigo')
                    ->label('Código')
                    ->maxLength(50)
                    ->unique(ignoreRecord: true)
                    ->helperText('Ej: silo_1, silo_norte'),

                Forms\Components\TextInput::make('capacidad_kg')
                    ->label('Capacidad (kg)')
                    ->numeric()
                    ->required()
                    ->minValue(0),

                Forms\Components\TextInput::make('stock_actual_kg')
                    ->label('Stock actual (kg)')
                    ->numeric()
                    ->required()
                    ->default(0)
                    ->minValue(0),

                Forms\Components\Select::make('cereal')
                    ->label('Cereal')
                    ->options([
                        'Maiz'     => 'Maíz',
                        'Soja'     => 'Soja',
                        'Trigo'    => 'Trigo',
                        'Girasol'  => 'Girasol',
                        'Cebada'   => 'Cebada',
                        'Sorgo'    => 'Sorgo',
                        'Otro'     => 'Otro',
                    ])
                    ->searchable()
                    ->nullable(),

                Forms\Components\TextInput::make('humedad')
                    ->label('Humedad (%)')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->step(0.01)
                    ->nullable(),

                Forms\Components\Select::make('estado')
                    ->label('Estado')
                    ->options([
                        'activo'        => 'Activo',
                        'vacio'         => 'Vacío',
                        'lleno'         => 'Lleno',
                        'en_reparacion' => 'En reparación',
                    ])
                    ->required()
                    ->default('vacio'),

                Forms\Components\TextInput::make('ubicacion')
                    ->label('Ubicación')
                    ->maxLength(255)
                    ->nullable(),
            ]),

            Forms\Components\Textarea::make('observaciones')
                ->label('Observaciones')
                ->rows(3)
                ->nullable()
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('cereal')
                    ->label('Cereal')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('capacidad_kg')
                    ->label('Capacidad (kg)')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('stock_actual_kg')
                    ->label('Stock actual (kg)')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('humedad')
                    ->label('Humedad (%)')
                    ->numeric(decimalPlaces: 2)
                    ->toggleable(),

                Tables\Columns\BadgeColumn::make('estado')
                    ->label('Estado')
                    ->colors([
                        'success' => 'activo',
                        'gray'    => 'vacio',
                        'warning' => 'lleno',
                        'danger'  => 'en_reparacion',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'activo'        => 'Activo',
                        'vacio'         => 'Vacío',
                        'lleno'         => 'Lleno',
                        'en_reparacion' => 'En reparación',
                        default         => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('ubicacion')
                    ->label('Ubicación')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'activo'        => 'Activo',
                        'vacio'         => 'Vacío',
                        'lleno'         => 'Lleno',
                        'en_reparacion' => 'En reparación',
                    ]),

                Tables\Filters\SelectFilter::make('cereal')
                    ->label('Cereal')
                    ->options([
                        'Maiz'    => 'Maíz',
                        'Soja'    => 'Soja',
                        'Trigo'   => 'Trigo',
                        'Girasol' => 'Girasol',
                        'Cebada'  => 'Cebada',
                        'Sorgo'   => 'Sorgo',
                        'Otro'    => 'Otro',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalWidth('2xl'),
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
            'index' => Pages\ListSilos::route('/'),
            // Sin rutas de create/edit separadas → los formularios abren como modal
        ];
    }
}
