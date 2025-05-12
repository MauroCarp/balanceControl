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
use Filament\Forms\Components\TextInput\Mask as Mask;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid as GridInfolist;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Infolist;
use Illuminate\Support\Carbon;


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
                                    ->options(Consignatarios::pluck('nombre','id')->toArray())
                                    ->label('Consignatario / Comisionista')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('nombre')
                                            ->label('Consignatario / Comisionista')
                                            ->required(),
                                        Forms\Components\TextInput::make('porcentajeConsignatario')
                                            ->label('% Comision')
                                            ->required(),
                                    ])
                                    ->createOptionUsing(function (array $data): int {
                                        $consignatario = Consignatarios::create(['nombre' => $data['nombre']]);
                                        return $consignatario->id;
                                    }),
                                    Forms\Components\TextInput::make('dte')
                                            ->label('Nº DTE')
                                            ->required()
                                            ->maxLength(11)
                                            ->mask('999999999-9')
                                            ->helperText('Ingrese 9 dígitos seguidos de 1 dígito final, sin el guion.'),
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        
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
                                    Forms\Components\TextInput::make('promedio')
                                        ->label('Promedio - Peso Neto / Total Hacienda')
                                        ->disabled()
                                        ->id('promedio')
                                        ->default(0)
                                        ->dehydrated(false),
                                    ]),
                                    Forms\Components\Grid::make(4)
                                        ->schema([
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
                                                ->label('Peso Desbaste Comercial')
                                                ->disabled()
                                                ->id('pesoDesbaste')
                                                ->default(0)
                                                ->dehydrated(false)
                                                ->reactive(),
                                            Forms\Components\TextInput::make('pesoDesbasteTecnico')
                                                ->label('Peso Desbaste Tecnico')
                                                ->disabled()
                                                ->id('pesoDesbasteTecnico')
                                                ->default(0)
                                                ->dehydrated(false)
                                                ->reactive(),
                                            ]),
                                            Forms\Components\Textarea::make('observaciones')
                                            ->label('Observaciones')
                                            ->maxLength(400)
                                            ->rows(1),
                            ])
                                ]),
                Wizard\Step::make('Ingreso Destino')
                    ->schema([
                        Forms\Components\Grid::make(5)
                            ->schema([
                                Forms\Components\TextInput::make('destino_pesoBruto')
                                    ->label('Peso Bruto')
                                    ->required()
                                    ->maxLength(191)
                                    ->default(0)
                                    ->numeric()
                                    ->id('destino_pesoBruto'),
                                Forms\Components\TextInput::make('destino_tara')
                                    ->label('Tara')
                                    ->required()
                                    ->maxLength(191)
                                    ->default(0)
                                    ->numeric()
                                    ->id('destino_tara'),
                                Forms\Components\TextInput::make('destino_pesoNeto')
                                    ->label('Peso Neto')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->default(0)
                                    ->reactive()
                                    ->id('destino_pesoNeto'),
                                Forms\Components\TextInput::make('destino_promedio')
                                    ->label('Promedio - Peso Neto / Total Hacienda')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->default(0)
                                    ->reactive()
                                    ->id('destino_promedio'),
                                Forms\Components\TextInput::make('destino_diferencia')
                                    ->label('Diferencia Peso Neto Origen/Destino')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->default(0)
                                    ->reactive()
                                    ->id('destino_diferencia')
                                ]),
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
                    ->searchable()
                    ->formatStateUsing(function ($state) {
                        return \Carbon\Carbon::parse($state)->format('d-m-Y');
                    }),
                Tables\Columns\TextColumn::make('consignatario')
                    ->label('Consignatario')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(function ($state) {
                        return Consignatarios::find($state)?->nombre ?? '-';
                    }),
                Tables\Columns\TextColumn::make('comisionista')
                    ->label('Comisionista')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(function ($state) {
                        return Comisionistas::find($state)?->nombre ?? '-';
                    }),
                Tables\Columns\TextColumn::make('dte')
                    ->label('Nº DTE')
                    ->sortable()
                    ->searchable(),
            ])
            ->defaultSort('fecha', 'desc') // Ordenar por la columna 'nombre' de forma ascendente

            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('')
                    ->icon('heroicon-o-eye')
                    ->color('primary'),
                Tables\Actions\EditAction::make()
                    ->color('warning')
                    ->label('')
                    ->icon('heroicon-o-pencil-square'),
                Tables\Actions\DeleteAction::make()
                    ->color('danger')
                    ->label('')
                    ->icon('heroicon-o-trash')
                    ->modalHeading('Eliminar Ingreso')
                    ->modalSubheading('¿Está seguro de eliminar este ingreso?'),
                
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {

        return $infolist
        ->schema([
            // Sección 1: Información General
            InfolistSection::make('Información General')
            ->schema([
                GridInfolist::make(4)
                    ->schema([
                        TextEntry::make('fecha')
                            ->label('Fecha')
                            ->size('lg')
                            ->weight('bold'),
                        TextEntry::make('consignatario')
                            ->label('Consignatario')
                            ->size('lg')
                            ->weight('bold')
                            ->getStateUsing(function ($record) {
                                return Consignatarios::find($record->consignatario)?->nombre ?? '-';
                            }),
                        TextEntry::make('comisionista')
                            ->label('Comisionista')
                            ->size('lg')
                            ->weight('bold')
                            ->getStateUsing(function ($record) {
                                return Comisionistas::find($record->comisionista)?->nombre ?? '-';
                            }),
                        TextEntry::make('dte')
                            ->label('N° DTE')
                            ->size('lg')
                            ->weight('bold'),
                    ]),
            ]),
            InfolistSection::make('Origen')
                ->schema([
                    // Sección 2: Información de Hacienda
                    InfolistSection::make('Información de Hacienda')
                        ->schema([
                            GridInfolist::make(3)
                                ->schema([
                                    TextEntry::make('origen_terneros')
                                        ->label('Terneros')
                                        ->size('lg')
                                        ->weight('bold'),
                                    TextEntry::make('origen_terneras')
                                        ->label('Terneras')
                                        ->size('lg')
                                        ->weight('bold'),
                                    TextEntry::make('totaHacienda')
                                        ->label('Cantidad Total de Hacienda')
                                        ->size('lg')
                                        ->weight('bold')
                                        ->getStateUsing(function ($record) {
                                            return number_format(($record->origen_terneros + $record->origen_terneras), 0, ',', '.');
                                        }),
                                ]),
                        ]),

                    // Sección 3: Información de Pesos
                    InfolistSection::make('Información de Pesos')
                        ->schema([
                            GridInfolist::make(3)
                                ->schema([
                                    TextEntry::make('origen_pesoBruto')
                                        ->label('Peso Bruto')
                                        ->size('lg')
                                        ->weight('bold')
                                        ->getStateUsing(function ($record) {
                                            return number_format($record->origen_pesoBruto, 0, ',', '.') . ' Kg';
                                        }),
                                    TextEntry::make('origen_pesoNeto')
                                        ->label('Peso Neto')
                                        ->size('lg')
                                        ->weight('bold')
                                        ->getStateUsing(function ($record) {
                                            return number_format($record->origen_pesoNeto, 0, ',', '.') . ' Kg';
                                        }),
                                    TextEntry::make('diferencia')
                                        ->label('Diferencia')
                                        ->size('lg')
                                        ->weight('bold')
                                        ->getStateUsing(function ($record) {
                                            return number_format(($record->origen_pesoBruto - $record->origen_pesoNeto), 0, ',', '.') . ' Kg';
                                        }),
                                    TextEntry::make('origen_distancia')
                                        ->label('Distancia Recorrida')
                                        ->size('lg')
                                        ->weight('bold')
                                        ->getStateUsing(function ($record) {
                                            return $record->origen_distancia . ' Km';
                                        }),
                                    TextEntry::make('origen_desbaste')
                                        ->label('% Desbaste')
                                        ->size('lg')
                                        ->weight('bold'),
                                    TextEntry::make('origen_pesoDesbaste')
                                        ->label('Peso Desbaste')
                                        ->size('lg')
                                        ->weight('bold')
                                        ->getStateUsing(function ($record) {
                                            return number_format(($record->origen_pesoNeto - ($record->origen_pesoNeto * ($record->origen_desbaste / 100))), 0, ',', '.') . ' Kg';
                                        }),
                                ]),
                        ]),
                ]),
            InfolistSection::make('Destino')
                ->schema([
                    // Sección 2: Información de Hacienda
                    InfolistSection::make('Información de Hacienda')
                        ->schema([
                            GridInfolist::make(3)
                                ->schema([
                                    TextEntry::make('destino_terneros')
                                        ->label('Terneros')
                                        ->size('lg')
                                        ->weight('bold'),
                                    TextEntry::make('destino_terneras')
                                        ->label('Terneras')
                                        ->size('lg')
                                        ->weight('bold'),
                                    TextEntry::make('totaHaciendaDestino')
                                        ->label('Cantidad Total de Hacienda')
                                        ->size('lg')
                                        ->weight('bold')
                                        ->getStateUsing(function ($record) {
                                            return number_format(($record->destino_terneros + $record->destino_terneras), 0, ',', '.');
                                        }),
                                ]),
                        ]),

                    // Sección 3: Información de Pesos
                    InfolistSection::make('Información de Pesos')
                        ->schema([
                            GridInfolist::make(4)
                                ->schema([
                                    TextEntry::make('destino_pesoBruto')
                                        ->label('Peso Bruto')
                                        ->size('lg')
                                        ->weight('bold')
                                        ->getStateUsing(function ($record) {
                                            return number_format($record->origen_pesoBruto, 0, ',', '.') . ' Kg';
                                        }),
                                    TextEntry::make('destino_tara')
                                        ->label('Tara')
                                        ->size('lg')
                                        ->weight('bold')
                                        ->getStateUsing(function ($record) {
                                            return number_format($record->destino_tara, 0, ',', '.') . ' Kg';
                                        }),
                                    TextEntry::make('destino_diferencia')
                                        ->label('Diferencia')
                                        ->size('lg')
                                        ->weight('bold')
                                        ->getStateUsing(function ($record) {
                                            return number_format(($record->origen_pesoBruto - $record->origen_pesoNeto), 0, ',', '.') . ' Kg';
                                        }),
                                    TextEntry::make('origen_distancia')
                                        ->label('Distancia Recorrida')
                                        ->size('lg')
                                        ->weight('bold')
                                        ->getStateUsing(function ($record) {
                                            return $record->origen_distancia . ' Km';
                                        }),
                                ]),
                        ]),
                ]),
            InfolistSection::make('Contable')
                ->schema([
                    InfolistSection::make('Información de Gastos')
                        ->schema([
                            GridInfolist::make(3)
                                ->schema([
                                    TextEntry::make('precioKg')
                                        ->label('Precio Kg')
                                        ->size('lg')
                                        ->weight('bold')
                                        ->getStateUsing(function ($record) {
                                            return '$ ' . number_format($record->precioKg, 2, ',', '.');
                                        }),
                                    TextEntry::make('totalNeto')
                                        ->label('$ Total Neto')
                                        ->size('lg')
                                        ->weight('bold')
                                        ->getStateUsing(function ($record) {
                                            return '$ ' . number_format(($record->precioKg * $record->origen_pesoNeto), 2, ',', '.');
                                        }),
                                    TextEntry::make('iva')
                                        ->label('$ Total c/IVA')
                                        ->size('lg')
                                        ->weight('bold')
                                        ->getStateUsing(function ($record) {
                                            $costoTotal = $record->precioKg * $record->origen_pesoNeto;
                                            $totalConIva = $costoTotal + (($costoTotal * 10.5) /100);
                                            return '$ ' . number_format($totalConIva, 2, ',', '.');
                                        }),
                                    TextEntry::make('comision')
                                        ->label('% Comisión')
                                        ->size('lg')
                                        ->weight('bold')
                                        ->getStateUsing(function ($record) {
                                            $comision = Comisionistas::find($record->comisionista)?->porcentajeComision ?? 0;
                                            $costoTotal = $record->precioKg * $record->origen_pesoNeto;
                                            $totalConIva = $costoTotal + (($costoTotal * 10.5) /100);

                                            return $comision . '% - $ ' . number_format((($totalConIva * $comision) / 100),2,',','.');
                                        }),
                                    TextEntry::make('flete')
                                        ->label('Flete')
                                        ->size('lg')
                                        ->weight('bold')
                                        ->getStateUsing(function ($record) {

                                            if($record->precioFlete != 0){
                                                return 'SI';
                                            } else {
                                                return 'NO';
                                            }

                                        }),
                                    TextEntry::make('precioFlete')
                                        ->label('$ Flete')
                                        ->size('lg')
                                        ->weight('bold')
                                        ->getStateUsing(function ($record) {
                                            return '$ ' . number_format($record->precioFlete, 2, ',', '.');
                                        }),
                                    TextEntry::make('precioOtrosGastos')
                                        ->label('Otros Gastos')
                                        ->size('lg')
                                        ->weight('bold')
                                        ->getStateUsing(function ($record) {
                                            return '$ ' . number_format($record->precioOtrosGastos, 2, ',', '.');
                                        }),
                                    TextEntry::make('totalConIvaApagar')
                                        ->label('Total c/IVA a Pagar')
                                        ->size('lg')
                                        ->weight('bold')
                                        ->getStateUsing(function ($record) {
                                            $comision = Comisionistas::find($record->comisionista)?->porcentajeComision ?? 0;
                                            $costoTotal = $record->precioKg * $record->origen_pesoNeto;
                                            $totalConIva = $costoTotal + (($costoTotal * 10.5) /100);


                                            return '$ ' . number_format(($totalConIva + (($totalConIva * $comision) / 100) + $record->precioFlete + $record->precioOtrosGastos), 2, ',', '.');
                                        }),

                                    TextEntry::make('precioNetoKg')
                                        ->label('$ Neto de compra por Kg')
                                        ->size('lg')
                                        ->weight('bold')
                                        ->getStateUsing(function ($record) {
                                            $comision = Comisionistas::find($record->comisionista)?->porcentajeComision ?? 0;
                                            $costoTotal = $record->precioKg * $record->origen_pesoNeto;
                                            $totalConIva = $costoTotal + (($costoTotal * 10.5) /100);

                                            return '$ ' . number_format(($record->precioKg + (((($totalConIva * $comision) / 100) + $record->precioFlete + $record->precioOtrosGastos) / $record->origen_pesoNeto)), 2, ',', '.');
                                        }),

                                ]),
                        ]),
                ])
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
            'view' => Pages\ViewBarloventoIngresos::route('/{record}'),

        ];
    }
}
