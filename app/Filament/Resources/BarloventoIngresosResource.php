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
use Filament\Forms\Components\Wizard;

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
            Wizard::make([
                Wizard\Step::make('Ingreso Origen')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\DatePicker::make('fecha')
                                    ->label('Fecha')
                                    ->required(),
                                Forms\Components\Select::make('consignatario')
                                    ->options(Consignatarios::pluck('nombre')->toArray())
                                    ->label('Consignatario')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('nombre')
                                            ->label('Consignatario')
                                            ->required(),
                                    ])
                                    ->createOptionUsing(function (array $data): int {
                                        $consignatario = Consignatarios::create(['nombre' => $data['nombre']]);
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
                                        $comisionista = Comisionistas::create(['nombre' => $data['nombre']]);
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
                                            ->default(0)
                                            ->id('origen_terneros')
                                            ->required()
                                            ->numeric()
                                            ->maxLength(3),
                                        Forms\Components\TextInput::make('origen_terneras')
                                            ->label('Terneras')
                                            ->required()
                                            ->id('origen_terneras')
                                            ->default(0)
                                            ->numeric()
                                            ->maxLength(3),
                                        Forms\Components\TextInput::make('cantidadTotal')
                                            ->label('Total Hacienda')
                                            ->id('cantidadTotal')
                                            ->dehydrated(false)
                                            ->default(0)
                                            ->disabled()
                                        ]),
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                    Forms\Components\TextInput::make('origen_pesoBruto')
                                        ->label('Peso Bruto')
                                        ->required()
                                        ->default(0)
                                        ->id('origen_pesoBruto')
                                        ->numeric(),
                                    Forms\Components\TextInput::make('origen_pesoNeto')
                                        ->label('Peso Neto')
                                        ->required()
                                        ->default(0)
                                        ->id('origen_pesoNeto')
                                        ->maxLength(191)
                                        ->numeric(),
                                    Forms\Components\TextInput::make('diferencia')
                                        ->label('Diferencia')
                                        ->disabled()
                                        ->id('diferencia')
                                        ->default(0)
                                        ->dehydrated(false),
                                    ]),
                                Forms\Components\TextInput::make('origen_distancia')
                                    ->label('Distancia Recorrida')
                                    ->required()
                                    ->numeric()
                                    ->id('origen_distancia')
                                    ->maxLength(191),
                                Forms\Components\Select::make('origen_desbaste')
                                    ->options([2=>2,3=>3,4=>4,5=>5])
                                    ->label('% Desbaste')
                                    ->required()
                                    ->id('origen_desbaste')
                                    ->afterStateUpdated(function (callable $set, $state, $get) {
                                        $set('pesoDesbaste', (float) $get('origen_pesoNeto') - ((float) $get('origen_pesoNeto') * ((float) $get('origen_desbaste') / 100)));
                                    }),
                                Forms\Components\TextInput::make('pesoDesbaste')
                                    ->label('Peso Desbaste')
                                    ->disabled()
                                    ->id('pesoDesbaste')
                                    ->default(0)
                                    ->dehydrated(false)
                                    ->reactive(),
                            ])
                                ]),
                Wizard\Step::make('Ingreso Destino')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('destino_terneros')
                                    ->label('Terneros')
                                    ->required()
                                    ->default(0)
                                    ->numeric()
                                    ->maxLength(3)
                                    ->id('destino_terneros'),
                                Forms\Components\TextInput::make('destino_terneras')
                                    ->label('Terneras')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->maxLength(3)
                                    ->id('destino_terneras'),
                                Forms\Components\TextInput::make('cantidadTotalDestino')
                                    ->label('Total Hacienda')
                                    ->default(0)
                                    ->dehydrated(false)
                                    ->disabled()
                                    ->id('cantidadTotalDestino'),
                            ]),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('destino_pesoBruto')
                                    ->label('Peso Bruto')
                                    ->required()
                                    ->maxLength(191)
                                    ->default(0)
                                    ->numeric()
                                    ->id('destino_pesoBruto'),
                                Forms\Components\TextInput::make('destino_tara')
                                    ->label('Peso Neto')
                                    ->required()
                                    ->maxLength(191)
                                    ->default(0)
                                    ->numeric()
                                    ->id('destino_tara'),
                                Forms\Components\TextInput::make('destino_diferencia')
                                    ->label('Diferencia')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->default(0)
                                    ->reactive()
                                    ->id('destino_diferencia')
                            ]),
                    ]),
                Wizard\Step::make('Ingreso Gastos')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([

                                Forms\Components\TextInput::make('precioKg')
                                    ->label('Precio Kg')
                                    ->maxLength(25)
                                    ->numeric(),
                                Forms\Components\TextInput::make('precioFlete')
                                    ->label('Precio Flete')
                                    ->maxLength(25)
                                    ->numeric(),
                                Forms\Components\TextInput::make('precioOtrosGastos')
                                    ->label('Precio Otros Gastos')
                                    ->maxLength(25)
                                    ->numeric(),
                            ])
                    ])->hidden(fn (string $context) => $context === 'create'), 
            ])
            ->columnSpan('full')

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fecha')
                    ->label('Fecha')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('consignatario')
                    ->label('Consignatario')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('comisionista')
                    ->label('Comisionista')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('dte')
                    ->label('Nº DTE')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    // Tables\Actions\Action::make('Ver')
                    //     ->icon('heroicon-o-eye')
                    //     ->url(fn (BarloventoIngresos $record): string => route('filament.resources.barlovento-ingresos.view', $record))
                    //     ->color('success'),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
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
            // 'view' => Pages\ViewBarloventoIngresos::route('/{record}'),

        ];
    }
}
