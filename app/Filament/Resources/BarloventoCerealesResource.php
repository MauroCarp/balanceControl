<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BarloventoCerealesResource\Pages;
use App\Filament\Resources\BarloventoCerealesResource\RelationManagers;
use App\Models\BarloventoCereales;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BarloventoCerealesResource extends Resource
{
    protected static ?string $model = BarloventoCereales::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-end-on-rectangle';
    protected static ?string $navigationGroup = 'Barlovento'; // Agrupa en "Barlovento"
    protected static ?string $navigationLabel = 'Ingresos Cereal'; // Nombre del 
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\DatePicker::make('fecha')
                            ->label('Fecha')
                            ->required(),
                        Forms\Components\Select::make('cereal')
                            ->label('Cereal')
                            ->options([
                                'Maiz' => 'Maiz',
                                'Soja' => 'Soja',
                                'Cascara de mani' => 'Cascara de Mani',
                                'Piedras' => 'Piedras',
                                'Urea' => 'Urea',
                                'Harina de Soja' => 'Harina de Soja',
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
                        Forms\Components\TextInput::make('cartaPorte')
                            ->label('Carta de Porte')
                            ->required()
                            ->maxLength(191),
                        Forms\Components\TextInput::make('vendedor')
                            ->label('Vendedor')
                            ->required(),
                        Forms\Components\TextInput::make('pesoBruto')
                            ->label('Peso Bruto')
                            ->required()
                            ->maxLength(191)
                            ->numeric()
                            ->reactive(),
                        Forms\Components\TextInput::make('pesoTara')
                            ->label('Tara')
                            ->required()
                            ->maxLength(191)
                            ->numeric()
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
                        Forms\Components\TextInput::make('humedad')
                            ->label('% de Humedad')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('mermaHumedad')
                            ->label('% Merma de Humedad')
                            ->disabled()
                            ->dehydrated(false)
                            ->reactive()
                            // ->afterStateUpdated(function (callable $set, $state, $get) {
                            //     $set('pesoNetoDesbastado', (float) $get('pesoNeto') - ((float) $get('pesoNeto') * ((float) 8 / 100)));
                            // }),
                            ,
                        Forms\Components\TextInput::make('pesoNetoHumedad')
                            ->label('Peso Neto de Humedad')
                            ->disabled()
                            ->dehydrated(false)
                            ->reactive()
                            // ->afterStateUpdated(function (callable $set, $state, $get) {
                            //     $set('pesoNetoDesbastado', (float) $get('pesoNeto') - ((float) $get('pesoNeto') * ((float) 8 / 100)));
                            // }),
                            ,
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
            'index' => Pages\ListBarloventoCereales::route('/'),
            'create' => Pages\CreateBarloventoCereales::route('/create'),
            'edit' => Pages\EditBarloventoCereales::route('/{record}/edit'),
        ];
    }
}
