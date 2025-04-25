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
                                Forms\Components\Radio::make('destino') // Campo de tipo radio
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
                                    ->visible(fn ($get) => $get('destino') === 'Faena Propia'),
                                Forms\Components\Radio::make('ventaTerceros') // Campo de tipo radio
                                    ->label('Venta Terceros')
                                    ->options([
                                        'arreBeef' => 'Arre Beef',
                                        'sanJose' => 'San Jose Carnes',
                                    ])
                                    ->visible(fn ($get) => $get('destino') === 'Venta a Terceros'),

                                Forms\Components\Select::make('frigorifico')
                                    ->options(['La Pelegrinense','Matievich','Bustos y Beltran','Arre Beef'])
                                    ->label('Frigorifico')
                                    ->searchable()
                                    ->preload()                
                                    ->required()
                                    ->visible(fn ($get) => $get('destino') === 'Faena Propia'),
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
            'index' => Pages\ListBarloventoEgresos::route('/'),
            'create' => Pages\CreateBarloventoEgresos::route('/create'),
            'edit' => Pages\EditBarloventoEgresos::route('/{record}/edit'),
        ];
    }
}
