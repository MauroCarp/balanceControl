<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaihuenCerealesResource\Pages;
use App\Filament\Resources\PaihuenCerealesResource\RelationManagers;
use App\Models\PaihuenCereales;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaihuenCerealesResource extends Resource
{
    protected static ?string $model = PaihuenCereales::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-start-on-rectangle';
    protected static ?string $navigationGroup = 'Paihuen'; // Agrupa en "Barlovento"
    protected static ?string $navigationLabel = 'Ingresos Cereales'; // Nombre del 
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(4)
                    ->schema([
                        Forms\Components\Select::make('cereal')
                            ->label('Cereal')
                            ->options([
                                'Maiz' => 'Maiz',
                                'Soja' => 'Soja',
                                'Trigo' => 'Cascara de Mani',
                                ])
                            ->default('Maiz')
                            ->required()
                            ->searchable() // Permite buscar o escribir valores personalizados
                            ->dehydrated(false)
                            ->createOptionForm([
                                Forms\Components\TextInput::make('otroIngreso')
                                ->label('Otro Ingreso')
                                ->required(),
                            ]),
                        Forms\Components\DatePicker::make('fecha')
                            ->label('Fecha')
                            ->required(),
                        Forms\Components\TextInput::make('cartaPorte')
                            ->label('Carta de Porte')
                            ->required()
                            ->maxLength(191),
                        Forms\Components\TextInput::make('vendedor')
                            ->label('Vendedor')
                            ->required(),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('pesoBruto')
                                    ->id('pesoBruto')
                                    ->label('Peso Bruto')
                                    ->required()
                                    ->default(0)
                                    ->maxLength(191)
                                    ->numeric(),
                                Forms\Components\TextInput::make('pesoTara')
                                    ->id('pesoTara')
                                    ->label('Tara')
                                    ->required()
                                    ->default(0)
                                    ->maxLength(191)
                                    ->numeric(),
                                Forms\Components\TextInput::make('pesoNeto')
                                    ->id('pesoNeto')
                                    ->label('Peso Neto')
                                    ->required()
                                    ->default(0)
                                    ->maxLength(191)
                                    ->disabled()
                                    ->dehydrated(false),
                            ]),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('humedad')
                                    ->id('humedad')
                                    ->label('% de Humedad')
                                    ->numeric()
                                    ->required()
                                    ->default(0),
                                Forms\Components\TextInput::make('mermaHumedad')
                                    ->id('mermaHumedad')
                                    ->label('% Merma de Humedad')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->default(0),
                                Forms\Components\TextInput::make('pesoNetoHumedad')
                                    ->id('pesoNetoHumedad')
                                    ->label('Peso Neto de Humedad')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->default(0),
                            ]),
                        Forms\Components\Checkbox::make('granosRotos')
                            ->label('Granos Dañados'),
                        Forms\Components\Checkbox::make('granosQuebrados')
                            ->label('Granos Quebrados'),
                        Forms\Components\Checkbox::make('tierra')
                            ->label('Contiene Tierra'),
                        Forms\Components\Select::make('calidad')
                            ->label('Calidad')
                            ->options([
                                'mala' => 'Mala',
                                'buena' => 'Buena',
                                'muyBuena' => 'Muy Buena',
                            ])
                            ->required(),
                        Forms\Components\Grid::make(3)
                                ->schema([
                                    Forms\Components\TextInput::make('materiasExtranas')
                                        ->label('Materias Extrañas')
                                        ->numeric()
                                        ->step(5),
                                    Forms\Components\Radio::make('destino')
                                        ->label('Destino/Almacenamiento')
                                        ->options([
                                            'plantaSilo' => 'Planta de Silo',
                                            'siloBolsa' => 'Silo Bolsa',
                                        ])
                                        ->required(),
                                    Forms\Components\Textarea::make('observaciones')
                                        ->label('Observaciones')
                                        ->maxLength(400)
                                        ->rows(3),
                                ])  
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
            'index' => Pages\ListPaihuenCereales::route('/'),
            'create' => Pages\CreatePaihuenCereales::route('/create'),
            'edit' => Pages\EditPaihuenCereales::route('/{record}/edit'),
        ];
    }
}
