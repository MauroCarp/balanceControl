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
            Forms\Components\TextInput::make('pesoTara')
                ->label('Tara')
                ->required()
                ->maxLength(191)
                ->reactive(),
            Forms\Components\TextInput::make('pesoBruto')
                ->label('Peso Bruto')
                ->required()
                ->maxLength(191)
                ->reactive(),
            Forms\Components\TextInput::make('pesoNeto')
                ->label('Peso Neto')
                ->required()
                ->maxLength(191)
                ->disabled()
                ->dehydrated(false)
                ->reactive()
                ->afterStateUpdated(function (callable $set, $state, $get) {
                    $set('pesoNeto', (float) $get('pesoTara') - (float) $get('PesoNeto'));
                }),
            Forms\Components\TextInput::make('pesoNetoDesbastado')
                ->label('Peso Neto Desbastado')
                ->disabled()
                ->dehydrated(false)
                ->reactive()
                ->afterStateUpdated(function (callable $set, $state, $get) {
                    $set('pesoNetoDesbastado', (float) $get('pesoNeto') - ((float) $get('pesoNeto') * ((float) 8 / 100)));
                }),
            Forms\Components\Radio::make('categoria')
                ->label('Categoria')
                ->options([
                    'vaquillona' => 'Vaquillona',
                    'novillo' => 'Novillo',
                ]),
            Forms\Components\TextInput::make('cantidad')
                ->label('Cantidad')
                ->required()
                ->maxLength(4),
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
