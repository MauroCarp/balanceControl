<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BarloventoIngresosResource\Pages;
use App\Filament\Resources\BarloventoIngresosResource\RelationManagers;
use App\Models\BarloventoIngresos;
use App\Models\Comisionistas;
use App\Models\Consignatarios;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BarloventoIngresosResource extends Resource
{
    protected static ?string $model = BarloventoIngresos::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-end-on-rectangle';
    protected static ?string $navigationGroup = 'Barlovento'; // Agrupa en "Barlovento"
    protected static ?string $navigationLabel = 'Ingresos Animales'; // Nombre del enlace
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Grid::make(3)
                ->schema([

                    Forms\Components\DatePicker::make('fecha')
                        ->label('Fecha')
                        ->required(),
                    Forms\Components\Select::make('consignatario')
                        ->options(Consignatarios::pluck('nombre', 'id')->toArray())
                        ->label('Consignatario')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->createOptionForm([
                            Forms\Components\TextInput::make('consignatario')
                                ->label('Consignatario')
                                ->required(),
                        ])
                        ->createOptionUsing(function (array $data): int {
                            $consignatario = Consignatarios::create(['nombre' => $data['name']]);
                            return $consignatario->id;
                        }),
                    Forms\Components\Select::make('comisionista')
                        ->options(Comisionistas::pluck('nombre', 'id')->toArray())
                        ->label('Comisionista')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->createOptionForm([
                            Forms\Components\TextInput::make('comisionista')
                                ->label('Comisionista')
                                ->required(),
                        ])
                        ->createOptionUsing(function (array $data): int {
                            $comisionista = Comisionistas::create(['name' => $data['name']]);
                            return $comisionista->id;
                        }),

                    Forms\Components\Grid::make(4)
                        ->schema([
                            Forms\Components\TextInput::make('dte')
                                ->label('Nº DTE')
                                ->required()
                                ->maxLength(191),
                            Forms\Components\TextInput::make('origen_terneros')
                                ->label('Terneros')
                                ->required()
                                ->numeric()
                                ->reactive()
                                ->maxLength(3),
                            Forms\Components\TextInput::make('origen_terneras')
                                ->label('Terneras')
                                ->required()
                                ->numeric()
                                ->reactive()
                                ->maxLength(3),
                            Forms\Components\TextInput::make('cantidadTotal')
                                ->label('Total Hacienda')
                                ->dehydrated(false)
                                ->disabled()
                                ->afterStateUpdated(function (callable $set, $state, $get) {
                                    $set('cantidadTotal', (float) $get('origen_terneros') + (float) $get('origen_terneras'));
                                }),
                            ]),
                    Forms\Components\Grid::make(3)
                        ->schema([
                        Forms\Components\TextInput::make('origen_pesoBruto')
                            ->label('Peso Bruto')
                            ->required()
                            ->maxLength(191)
                            ->numeric()
                            ->reactive(),
                        Forms\Components\TextInput::make('origen_pesoNeto')
                            ->label('Peso Neto')
                            ->required()
                            ->maxLength(191)
                            ->numeric()
                            ->reactive(),
                        Forms\Components\TextInput::make('diferencia')
                            ->label('Diferencia')
                            ->disabled()
                            ->dehydrated(false)
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, $state, $get) {
                                $set('diferencia', (float) $get('pesoBruto') - (float) $get('pesoNeto'));
                            }),
                        ]),
                    Forms\Components\TextInput::make('origen_distancia')
                        ->label('Distancia Recorrida')
                        ->required()
                        ->numeric()
                        ->maxLength(191),
                    Forms\Components\Select::make('origen_desbaste')
                        ->options([2,3,4,5])
                        ->label('% Desbaste')
                        ->required(),
                    Forms\Components\TextInput::make('pesoDesbaste')
                        ->label('Peso Desbaste')
                        ->disabled()
                        ->dehydrated(false)
                        ->reactive()
                        ->afterStateUpdated(function (callable $set, $state, $get) {
                            $set('pesoDesbaste', (float) $get('pesoNeto') - ((float) $get('pesoNeto') * ((float) $get('desbaste') / 100)));
                        }),
                    Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\TextInput::make('destino_terneros')
                                ->label('Terneros')
                                ->required()
                                ->numeric()
                                ->reactive()
                                ->hidden(fn (string $context) => $context === 'create') // Oculta el campo solo en el formulario de creación
                                ->maxLength(3),
                            Forms\Components\TextInput::make('destino_terneras')
                                ->label('Terneras')
                                ->required()
                                ->numeric()
                                ->reactive()
                                ->hidden(fn (string $context) => $context === 'create') // Oculta el campo solo en el formulario de creación
                                ->maxLength(3),
                            Forms\Components\TextInput::make('cantidadTotalDestino')
                                ->label('Total Hacienda')
                                ->dehydrated(false)
                                ->disabled()
                                ->hidden(fn (string $context) => $context === 'create') // Oculta el campo solo en el formulario de creación
                                ->afterStateUpdated(function (callable $set, $state, $get) {
                                    $set('cantidadTotalDestino', (float) $get('destino_terneros') + (float) $get('destino_terneras'));
                                }),
                            ]),
                        Forms\Components\Grid::make(3)
                            ->schema([
                            Forms\Components\TextInput::make('destino_pesoBruto')
                                ->label('Peso Bruto')
                                ->required()
                                ->maxLength(191)
                                ->numeric()
                                ->hidden(fn (string $context) => $context === 'create') // Oculta el campo solo en el formulario de creación
                                ->reactive(),
                            Forms\Components\TextInput::make('destino_tara')
                                ->label('Peso Neto')
                                ->required()
                                ->maxLength(191)
                                ->hidden(fn (string $context) => $context === 'create') // Oculta el campo solo en el formulario de creación
                                ->numeric()
                                ->reactive(),
                            Forms\Components\TextInput::make('diferencia')
                                ->label('Diferencia')
                                ->disabled()
                                ->dehydrated(false)
                                ->reactive()
                                ->hidden(fn (string $context) => $context === 'create') // Oculta el campo solo en el formulario de creación
                                ->afterStateUpdated(function (callable $set, $state, $get) {
                                    $set('diferencia', (float) $get('pesoBruto') - (float) $get('pesoNeto'));
                                }),
                            ]),
                        Forms\Components\TextInput::make('precioKg')
                            ->label('Precio Kg')
                            ->maxLength(25)
                            ->hidden(fn (string $context) => $context === 'create') // Oculta el campo solo en el formulario de creación
                            ->numeric(),
                        Forms\Components\TextInput::make('precioFlete')
                            ->label('Precio Flete')
                            ->maxLength(25)
                            ->hidden(fn (string $context) => $context === 'create') // Oculta el campo solo en el formulario de creación
                            ->numeric(),
                        Forms\Components\TextInput::make('precioOtrosGastos')
                            ->label('Precio Otros Gastos')
                            ->maxLength(25)
                            ->hidden(fn (string $context) => $context === 'create') // Oculta el campo solo en el formulario de creación
                            ->numeric(),
                        


                ])

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
            'index' => Pages\ListBarloventoIngresos::route('/'),
            'create' => Pages\CreateBarloventoIngresos::route('/create'),
            'edit' => Pages\EditBarloventoIngresos::route('/{record}/edit'),
        ];
    }
}
