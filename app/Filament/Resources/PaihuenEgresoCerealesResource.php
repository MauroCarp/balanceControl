<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaihuenEgresoCerealesResource\Pages;
use App\Filament\Resources\PaihuenEgresoCerealesResource\RelationManagers;
use App\Models\Insumos;
use App\Models\PaihuenEgresoCereales;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Components\Grid as GridInfolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;

class PaihuenEgresoCerealesResource extends Resource
{
    protected static ?string $model = PaihuenEgresoCereales::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-end-on-rectangle';
    protected static ?string $navigationGroup = 'Paihuen'; // Agrupa en "Barlovento"
    protected static ?string $navigationLabel = 'Egresos Insumos'; // Nombre del 
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(4)
                    ->schema([
                        Forms\Components\Select::make('cereal')
                            ->label('Insumo')
                            ->options(\App\Models\Insumos::pluck('insumo', 'insumo')->toArray())
                            ->default('Maiz')
                            ->required()
                            ->reactive()
                            ->id('cereal')
                            ->searchable() // Permite buscar o escribir valores personalizados
                            ->createOptionForm([
                                Forms\Components\TextInput::make('insumo')
                                ->label('Nuevo Insumo')
                                ->required(),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                $insumo = Insumos::create(['insumo' => $data['insumo']]);
                                return $insumo->id;
                            }),
                        Forms\Components\DatePicker::make('fecha')
                            ->label('Fecha')
                            ->required(),
                        Forms\Components\TextInput::make('cartaPorte')
                            ->label('Carta de Porte')
                            ->required()
                            ->maxLength(191),
                        Forms\Components\Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->maxLength(400)
                            ->rows(1),
                        ]),
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
                    ->label('Insumo')
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

        public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
        ->schema([
                GridInfolist::make(4)
                    ->schema([
                        TextEntry::make('cereal')
                            ->size('lg')
                            ->weight('bold')
                            ->label('Insumo')
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
                       TextEntry::make('observaciones')
                            ->size('lg')
                            ->weight('bold'),  
                        GridInfolist::make(3)
                                ->schema([
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
            'index' => Pages\ListPaihuenEgresoCereales::route('/'),
            'create' => Pages\CreatePaihuenEgresoCereales::route('/create'),
            'edit' => Pages\EditPaihuenEgresoCereales::route('/{record}/edit'),
        ];
    }
}
