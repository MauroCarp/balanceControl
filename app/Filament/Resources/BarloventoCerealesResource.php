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
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid as GridInfolist;
use Filament\Infolists\Components\Group;
use Illuminate\Support\Facades\DB;

class BarloventoCerealesResource extends Resource
{
    protected static ?string $model = BarloventoCereales::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-end-on-rectangle';
    protected static ?string $navigationGroup = 'Barlovento'; // Agrupa en "Barlovento"
    protected static ?string $navigationLabel = 'Ingresos Cereales'; // Nombre del 
    protected static ?int $navigationSort = 3;

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
                                'Cascara de mani' => 'Cascara de Mani',
                                'Piedras' => 'Piedras',
                                'Urea' => 'Urea',
                                'Harina de Soja' => 'Harina de Soja',
                                ])
                            ->default('Maiz')
                            ->required()
                            ->id('cereal')
                            ->searchable() // Permite buscar o escribir valores personalizados
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
                                    ->dehydrated()
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
                                        ->default(0)
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
        ->schema([
                GridInfolist::make(3)
                    ->schema([
                        GridInfolist::make(4)
                            ->schema([
                                TextEntry::make('cereal')
                                    ->size('lg')
                                    ->weight('bold')
                                    ->label('Cereal')
                                    ->size('lg')
                                    ->weight('bold'),
                                TextEntry::make('fecha')
                                    ->size('lg')
                                    ->weight('bold')
                                    ->label('Fecha')
                                    ->date('d-m-Y'),
                                TextEntry::make('cartaPorte')
                                    ->size('lg')
                                    ->weight('bold')
                                    ->label('Carta de Porte'),
                                TextEntry::make('vendedor')
                                    ->size('lg')
                                    ->weight('bold')
                                    ->label('Vendedor'),
                            ]),
                        TextEntry::make('pesoBruto')
                            ->size('lg')
                            ->weight('bold')
                            ->label('Peso Bruto')
                            ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.') . ' Kg'),
                            TextEntry::make('pesoTara')
                            ->size('lg')
                            ->weight('bold')
                            ->label('Tara')
                            ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.') . ' Kg'),
                        TextEntry::make('pesoNeto')
                            ->size('lg')
                            ->weight('bold')
                            ->label('Peso Neto')
                            ->getStateUsing(function ($record) {
                                return number_format(($record->pesoBruto - $record->pesoTara), 0, ',', '.') . ' Kg';
                            }),
                        TextEntry::make('humedad')
                            ->size('lg')
                            ->weight('bold')
                            ->label('% de Humedad'),
                        TextEntry::make('mermaHumedad')
                            ->size('lg')
                            ->weight('bold')
                            ->label('% Merma de Humedad')
                            ->getStateUsing(function ($record) {

                                $merma = DB::table('merma_humedad')
                                ->where('cereal', $record->cereal)
                                ->where('humedad', $record->humedad)
                                ->value('merma');

                                return $merma . '%';
                            }),
                        TextEntry::make('pesoNetoHumedad')
                            ->size('lg')
                            ->weight('bold')
                            ->label('Peso Neto de Humedad')
                            ->getStateUsing(function ($record) {

                                $merma = DB::table('merma_humedad')
                                ->where('cereal', $record->cereal)
                                ->where('humedad', $record->humedad)
                                ->value('merma');

                                $pesoNeto = $record->pesoBruto - $record->pesoTara;

                                $resultado = ($pesoNeto - ($pesoNeto * ($merma / 100)));

                                return number_format($resultado,0,',','.') . ' Kg';
                            }),
                        TextEntry::make('granosRotos')
                            ->size('lg')
                            ->weight('bold')
                            ->label('Granos Dañados')
                            ->formatStateUsing(fn ($state) => ($state ? 'Sí' : 'No')),
                        TextEntry::make('granosQuebrados')
                            ->size('lg')
                            ->weight('bold')
                            ->label('Granos Quebrados')
                            ->formatStateUsing(fn ($state) => ($state ? 'Sí' : 'No')),
                        TextEntry::make('tierra')
                            ->size('lg')
                            ->weight('bold')
                            ->label('Contiene Tierra')
                            ->formatStateUsing(fn ($state) => ($state ? 'Sí' : 'No')),
                        TextEntry::make('calidad')
                            ->size('lg')
                            ->weight('bold')
                            ->label('Calidad')
                            ->formatStateUsing(fn ($state) => (($state == 'muyBuena') ? 'Muy Buena' : ucfirst($state))),
                        TextEntry::make('materiasExtranas')
                            ->size('lg')
                            ->weight('bold')
                            ->label('Materias Extrañas'),
                        TextEntry::make('destino')
                            ->size('lg')
                            ->weight('bold')
                            ->label('Destino/Almacenamiento')
                            ->formatStateUsing(fn ($state) => ($state == 'plantaSilo' ? 'Planta/Silo' : 'Silo Bolsa')),
                        TextEntry::make('observaciones')
                            ->size('lg')
                            ->weight('bold'),                      
                    ]),
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
                Tables\Columns\TextColumn::make('cereal')
                    ->label('Cereal')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('cartaPorte')
                    ->label('Carta de Porte')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('pesoBruto')
                    ->label('Peso Bruto')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(function ($state) {
                        return number_format($state,0,',','.');
                    }),
                Tables\Columns\TextColumn::make('vendedor')
                    ->label('Vendedor')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('destino')
                    ->label('Destino')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(function ($state) {
                        return $state === 'siloBolsa' ? 'Silo Bolsa' : ($state === 'plantaSilo' ? 'Planta de Silo' : $state);
                    }),
            ])
            ->defaultSort('fecha', 'desc') // Ordenar por la columna 'nombre' de forma ascendente

            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                ->label('')
                ->color('primary'),
                Tables\Actions\EditAction::make()
                ->label(''),
                Tables\Actions\DeleteAction::make()
                ->label('')
                ->color('danger'),
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
            'view' => Pages\ViewBarloventoCereales::route('/{record}'),

        ];
    }
}
