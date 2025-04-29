<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BarloventoEgresosResource\Pages;
use App\Filament\Resources\BarloventoEgresosResource\RelationManagers;
use App\Models\BarloventoEgresos;
use App\Models\Comisionistas;
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
                            ->maxLength(191),
                        Forms\Components\TextInput::make('flete')
                            ->label('Flete/Camion')
                            ->required(),
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
                                Forms\Components\Radio::make('faenaPropia') // Campo de tipo radio
                                    ->label('Faena Propia')
                                    ->options([
                                        'carniceria' => 'Carniceria',
                                        'salta' => 'Salta',
                                        'exportacion' => 'Exportacion',
                                    ])
                                    ->visible(fn ($get) => $get('tipoDestino') === 'Faena Propia'),
                                Forms\Components\Radio::make('ventaTerceros') // Campo de tipo radio
                                    ->label('Venta Terceros')
                                    ->options([
                                        'arreBeef' => 'Arre Beef',
                                        'sanJose' => 'San Jose Carnes',
                                    ])
                                    ->visible(fn ($get) => $get('tipoDestino') === 'Venta a Terceros'),
                                Forms\Components\Select::make('frigorifico')
                                    ->options([
                                    'La Pelegrinense' => 'La Pelegrinense',
                                    'Matievich' => 'Matievich',
                                    'Bustos y Beltran' => 'Bustos y Beltran',
                                    'Arre Beef' => 'Arre Beef' 
                                    ])
                                    ->label('Frigorifico')
                                    ->searchable()
                                    ->preload()                
                                    ->required()
                                    ->visible(fn ($get) => $get('tipoDestino') === 'Faena Propia'),
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
                                    ->required()
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
                            return ucfirst($record->faenaPropia) . ' - ' . $record->frigorifico;
                        } elseif ($record->tipoDestino === 'Venta a Terceros') {
                            return $record->ventaTerceros;
                        }
                        return null;
                    }),
            ])
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
                            ->weight('bold'),
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
                               return ucfirst($record->faenaPropia);
                            }),
                        TextEntry::make('ventaTerceros')
                            ->label('Venta Terceros')
                            ->size('lg')
                            ->weight('bold')
                            ->visible(fn ($record) => $record->tipoDestino === 'Venta a Terceros'),
                        TextEntry::make('frigorifico')
                            ->label('Frigorifico')
                            ->size('lg')
                            ->weight('bold')
                            ->visible(fn ($record) => $record->tipoDestino === 'Faena Propia'),
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
                            ->weight('bold'),
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
