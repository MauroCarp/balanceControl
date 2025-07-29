<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BarloventoEgresosResource\Pages;
use App\Filament\Resources\BarloventoEgresosResource\RelationManagers;
use App\Models\BarloventoEgresos;
use App\Models\DestinosEgresos;
use App\Models\Consignatarios;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid as GridInfolist;
use Filament\Infolists\Components\Group;

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
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\DatePicker::make('fecha')
                            ->label('Fecha')
                            ->required(),
                        Forms\Components\TextInput::make('dte')
                            ->label('Nº DTE')
                            ->required()
                            ->maxLength(11)
                            ->mask('999999999-9')
                            ->helperText('Ingrese 9 dígitos seguidos de 1 dígito final, sin el guion.'),
                        Forms\Components\Select::make('flete')
                            ->options(DestinosEgresos::where('tipo', 'FLETE')->pluck('nombre', 'id')->toArray())
                            ->label('Flete/Camión')
                            ->searchable()
                            ->preload()                
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('nombre')
                                    ->label('Flete / Camión')
                                    ->required(),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                $destino = DestinosEgresos::create([
                                    'nombre' => $data['nombre'],
                                    'tipo' => 'FLETE',
                                ]);
                                return $destino->id;
                            }),
                        Forms\Components\TextInput::make('novillos')
                            ->label('Novillos')
                            ->numeric()
                            ->required()
                            ->id('novillos')
                            ->default(0)
                            ->maxLength(3),
                        Forms\Components\TextInput::make('vaquillonas')
                            ->label('Vaquillonas')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->id('vaquillonas')
                            ->maxLength(3),
                        Forms\Components\TextInput::make('cantidad')
                            ->label('Cantidad')
                            ->default(0)
                            ->disabled()
                            ->id('cantidad')
                            ->maxLength(4),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Radio::make('tipoDestino') // Campo de tipo radio
                                    ->label('Destino')
                                    ->options([
                                        'Faena Propia' => 'Faena Propia',
                                        'Venta a Terceros' => 'Venta a Terceros',
                                    ])
                                    ->required()
                                    ->reactive()
                                    ->helperText('Selecciona el destino para mostrar los campos correspondientes.'), // Mensaje de ayuda
                                Forms\Components\Select::make('faenaPropia')
                                    ->options(DestinosEgresos::where('tipo', 'FP')->pluck('nombre', 'id')->toArray())
                                    ->label('Faena Propia')
                                    ->searchable()
                                    ->preload()                
                                    ->required()
                                    ->visible(fn ($get) => $get('tipoDestino') === 'Faena Propia')
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('nombre')
                                            ->label('Destino Faena Propia')
                                            ->required(),
                                    ])
                                    ->createOptionUsing(function (array $data): int {
                                        $destino = DestinosEgresos::create([
                                            'nombre' => $data['nombre'],
                                            'tipo' => 'FP',
                                        ]);
                                        return $destino->id;
                                    }),
                                Forms\Components\Select::make('ventaTerceros')
                                    ->options(DestinosEgresos::where('tipo', 'VT')->pluck('nombre', 'id')->toArray())
                                    ->label('Venta a Terceros')
                                    ->searchable()
                                    ->preload()                
                                    ->required()
                                    ->visible(fn ($get) => $get('tipoDestino') === 'Venta a Terceros')
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('nombre')
                                            ->label('Destino Venta a Terceros')
                                            ->required(),
                                    ])
                                    ->createOptionUsing(function (array $data): int {
                                        $destino = DestinosEgresos::create([
                                            'nombre' => $data['nombre'],
                                            'tipo' => 'VT',
                                        ]);
                                        return $destino->id;
                                    }),
                                Forms\Components\Select::make('frigorifico')
                                    ->options(DestinosEgresos::where('tipo', 'FRIG')->pluck('nombre', 'id')->toArray())
                                    ->label('Frigorifico')
                                    ->searchable()
                                    ->preload()                
                                    ->required()
                                    ->visible(fn ($get) => $get('tipoDestino') === 'Faena Propia')
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('nombre')
                                            ->label('Frigorifico')
                                            ->required(),
                                    ])
                                    ->createOptionUsing(function (array $data): int {
                                        $destino = DestinosEgresos::create([
                                            'nombre' => $data['nombre'],
                                            'tipo' => 'FRIG',
                                        ]);
                                        return $destino->id;
                                    }),
                            ]),
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\TextInput::make('pesoBruto')
                                    ->label('Peso Bruto')
                                    ->required()
                                    ->default(0)
                                    ->maxLength(191)
                                    ->id('pesoBruto'),
                                Forms\Components\TextInput::make('pesoTara')
                                    ->label('Tara')
                                    ->required()
                                    ->default(0)
                                    ->maxLength(191)
                                    ->id('pesoTara'),
                                Forms\Components\TextInput::make('pesoNeto')
                                    ->label('Peso Neto')
                                    ->default(0)
                                    ->maxLength(191)
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->id('pesoNeto'),
                                Forms\Components\TextInput::make('pesoNetoDesbastado')
                                    ->label('Peso Neto Desbastado')
                                    ->default(0)
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->id('pesoNetoDesbastado'),
                            ]),

                    ])
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
                    ->date('d-m-Y'),
                Tables\Columns\TextColumn::make('novillos')
                    ->label('Novillos')
                    ->sortable(),
                Tables\Columns\TextColumn::make('vaquillonas')
                    ->label('Vaquillonas')
                    ->sortable(),
                Tables\Columns\TextColumn::make('pesoNeto')
                    ->label('Peso Neto')
                    ->getStateUsing(fn ($record) => $record->pesoBruto - $record->pesoTara),
                Tables\Columns\TextColumn::make('tipoDestino')
                    ->label('Tipo de Destino')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('destino')
                    ->label('Destino')
                    ->getStateUsing(function ($record) {
                        if ($record->tipoDestino === 'Faena Propia') {
                            $faenaPropiaNombre = DestinosEgresos::find($record->faenaPropia)?->nombre ?? '';
                            $frigorifico = DestinosEgresos::find($record->frigorifico)?->nombre ?? '';

                            return ucfirst($faenaPropiaNombre) . ' - ' . $frigorifico;
                        } elseif ($record->tipoDestino === 'Venta a Terceros') {
                            $ventaTerceros = DestinosEgresos::find($record->ventaTerceros)?->nombre ?? '';

                            return $ventaTerceros;
                        }
                        return null;
                    }),
            ])
            ->defaultSort('fecha', 'desc') // Ordenar por la columna 'nombre' de forma ascendente

            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                ->label('')
                ->color('primary')
                ->after(function ($record, $data) {
                    $record->cantidad = $record->novillos + $record->vaquillonas;
                    $record->save();
                }),
                Tables\Actions\EditAction::make()
                ->label(''),
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
            InfolistSection::make('Detalle de Egreso')
            ->schema([
                GridInfolist::make(4)
                    ->schema([
                        TextEntry::make('fecha')
                            ->label('Fecha')
                            ->size('lg')
                            ->weight('bold'),
                        TextEntry::make('dte')
                            ->label('N° DTE')
                            ->size('lg')
                            ->weight('bold'),
                        TextEntry::make('flete')
                            ->label('Flete/Camion')
                            ->size('lg')
                            ->weight('bold')
                            ->getStateUsing(function ($record) {
                                $flete = DestinosEgresos::find($record->flete)?->nombre ?? '';

                               return ucfirst($flete);
                            }),
                        TextEntry::make('tipoDestino')
                            ->label('Destino')
                            ->size('lg')
                            ->weight('bold'),
                        TextEntry::make('faenaPropia')
                            ->label('Faena Propia')
                            ->size('lg')
                            ->weight('bold')
                            ->visible(fn ($record) => $record->tipoDestino === 'Faena Propia')
                            ->getStateUsing(function ($record) {
                                $faenaPropia = DestinosEgresos::find($record->faenaPropia)?->nombre ?? '';

                               return ucfirst($faenaPropia);
                            }),
                        TextEntry::make('ventaTerceros')
                            ->label('Venta Terceros')
                            ->size('lg')
                            ->weight('bold')
                            ->visible(fn ($record) => $record->tipoDestino === 'Venta a Terceros')
                            ->getStateUsing(function ($record) {
                                $ventaTerceros = DestinosEgresos::find($record->ventaTerceros)?->nombre ?? '';

                               return ucfirst($ventaTerceros);
                            }),
                        TextEntry::make('frigorifico')
                            ->label('Frigorifico')
                            ->size('lg')
                            ->weight('bold')
                            ->visible(fn ($record) => $record->tipoDestino === 'Faena Propia')
                            ->getStateUsing(function ($record) {
                                $frigorifico = DestinosEgresos::find($record->frigorifico)?->nombre ?? '';

                               return ucfirst($frigorifico);
                            }),
                        TextEntry::make('pesoBruto')
                            ->label('Peso Bruto')
                            ->size('lg')
                            ->weight('bold')
                            ->getStateUsing(fn ($record) => number_format($record->pesoBruto, 0, ',', '.') . ' Kg'),
                        TextEntry::make('pesoTara')
                            ->label('Tara')
                            ->size('lg')
                            ->weight('bold')
                            ->getStateUsing(fn ($record) => number_format($record->pesoTara, 0, ',', '.') . ' Kg'),
                        TextEntry::make('pesoNeto')
                            ->label('Peso Neto')
                            ->size('lg')
                            ->weight('bold')
                            ->getStateUsing(function ($record) {
                                $pesoNeto = $record->pesoBruto - $record->pesoTara;
                                return number_format($pesoNeto, 0, ',', '.') . ' Kg';
                            }),
                        TextEntry::make('pesoNetoDesbastado')
                            ->label('Peso Neto Desbastado')
                            ->size('lg')
                            ->weight('bold')
                            ->getStateUsing(function ($record) {
                                $pesoNeto = $record->pesoBruto - $record->pesoTara;
                                $pesoDesbastado = $pesoNeto - (($pesoNeto * 8) / 100);

                                return number_format($pesoDesbastado, 0, ',', '.') . ' Kg';

                            }),
                        TextEntry::make('novillos')
                            ->label('Novillos')
                            ->size('lg')
                            ->weight('bold'),
                        TextEntry::make('vaquillonas')
                            ->label('Vaquillonas')
                            ->size('lg')
                            ->weight('bold'),
                        TextEntry::make('cantidad')
                            ->label('Cantidad')
                            ->size('lg')
                            ->weight('bold')
                            ->getStateUsing(function ($record) {
                                return $record->novillos + $record->vaquillonas;
                            }),
                    ]),
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
