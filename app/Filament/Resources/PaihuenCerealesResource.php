<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaihuenCerealesResource\Pages;
use App\Filament\Resources\PaihuenCerealesResource\RelationManagers;
use App\Http\Controllers\Api\MermaHumedadController;
use App\Models\Insumos;
use App\Models\PaihuenCereales;
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
use Illuminate\Support\HtmlString;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PaihuenCerealesResource extends Resource
{
    protected static ?string $model = PaihuenCereales::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-start-on-rectangle';
    protected static ?string $navigationGroup = 'Paihuen'; // Agrupa en "Barlovento"
    protected static ?string $navigationLabel = 'Ingresos Insumos'; // Nombre del 
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(4)
                    ->schema([
                        Forms\Components\Grid::make(5)
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
                                    ->createOptionUsing(function (array $data): string {
                                        $insumo = Insumos::create(['insumo' => $data['insumo']]);
                                        return $insumo->insumo;
                                    }),
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
                                Forms\Components\TextInput::make('corredor')
                                    ->label('Corredor'),
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
                        Forms\Components\Grid::make(4)
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
                                Forms\Components\TextInput::make('mermaManipuleo')
                                    ->id('mermaManipuleo')
                                    ->label('% Manipuleo')
                                    ->disabled()
                                    ->default(0)
                                    ->dehydrated(false),
                                Forms\Components\Select::make('calidad')
                                    ->label('Calidad')
                                    ->options([
                                        'mala' => 'Mala',
                                        'buena' => 'Buena',
                                        'muyBuena' => 'Muy Buena',
                                    ])
                                    ->required(),
                            ])->visible(fn ($get) => in_array($get('cereal'), ['Maiz', 'Soja', 'Cascara de mani'])),
                        Forms\Components\TextInput::make('materiasExtranas')
                            ->label('Materias Extrañas %')
                            ->id('mermaMaterias')
                            ->numeric()
                            ->default(0)
                            ->visible(fn ($get) => in_array($get('cereal'), ['Maiz', 'Soja', 'Cascara de mani'])),
                        Forms\Components\TextInput::make('tierra')
                            ->label('Contiene Tierra %')
                            ->id('mermaTierra')
                            ->numeric()
                            ->default(0)
                            ->visible(fn ($get) => in_array($get('cereal'), ['Maiz', 'Soja', 'Cascara de mani'])),
                        Forms\Components\TextInput::make('olor')
                            ->label('Olor %')
                            ->id('mermaOlor')
                            ->numeric()
                            ->default(0)
                            ->visible(fn ($get) => in_array($get('cereal'), ['Maiz', 'Soja', 'Cascara de mani'])),
                        Forms\Components\TextInput::make('pesoNetoHumedad')
                            ->id('pesoNetoHumedad')
                            ->label('Peso Neto por Mermas')
                            ->disabled()
                            ->dehydrated(false)
                            ->default(0)
                            ->visible(fn ($get) => in_array($get('cereal'), ['Maiz', 'Soja', 'Cascara de mani'])),
                        Forms\Components\Grid::make(4)
                            ->schema([
                                    Forms\Components\Checkbox::make('granosRotos')
                                        ->label('Granos Dañados')
                                        ->visible(fn ($get) => in_array($get('cereal'), ['Maiz', 'Soja', 'Cascara de mani'])),
                                    Forms\Components\Checkbox::make('granosQuebrados')
                                        ->label('Granos Quebrados')
                                        ->visible(fn ($get) => in_array($get('cereal'), ['Maiz', 'Soja', 'Cascara de mani'])),
                                    Forms\Components\Radio::make('destino')
                                        ->label('Destino/Almacenamiento')
                                        ->options([
                                            'plantaSilo' => 'Planta de Silo',
                                            'siloBolsa' => 'Silo Bolsa',
                                        ])
                                        ->required()
                                        ->visible(fn ($get) => in_array($get('cereal'), ['Maiz', 'Soja', 'Cascara de mani'])),
                                    Forms\Components\Textarea::make('observaciones')
                                        ->label('Observaciones')
                                        ->maxLength(400)
                                        ->rows(3),
                                    ]),
                        Forms\Components\Checkbox::make('confirmado')
                        ->label(fn () => new HtmlString('<span style="box-shadow:2px 2px grey;padding:2px;border-radius:5px;border:2px solid rgb(55, 175, 81);font-size:1.5em;color: green;weight:bolder">Confirmar</span>'))
                    ])
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
        ->schema([
                GridInfolist::make(4)
                    ->schema([
                        GridInfolist::make(5)
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
                                    TextEntry::make('vendedor')
                                        ->size('lg')
                                        ->weight('bold')
                                        ->label('Vendedor'),
                                    TextEntry::make('corredor')
                                        ->size('lg')
                                        ->weight('bold')
                                        ->label('Corredor')
                                        ->getStateUsing(function ($record) {
                                            return ($record->corredor) ? $record->corredor : 'No especificado';
                                        }),
                                ]),
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
                        TextEntry::make('humedad')
                            ->size('lg')
                            ->weight('bold')
                            ->label('% de Humedad')
                            ->visible(fn ($record) => in_array($record->cereal, ['Maiz', 'Soja', 'Cascara de mani'])),
                        TextEntry::make('mermaHumedad')
                            ->size('lg')
                            ->weight('bold')
                            ->label('% Merma de Humedad')
                            ->getStateUsing(function ($record) {

                                $merma = 0; 

                                if($record->humedad > 14.5) {
                                    $merma = DB::table('merma_humedad')
                                    ->where('cereal', $record->cereal)
                                    ->where('humedad', $record->humedad)
                                    ->value('merma');
                                }

                                return $merma . '%';
                            })
                            ->visible(fn ($record) => in_array($record->cereal, ['Maiz', 'Soja', 'Cascara de mani'])),
                        TextEntry::make('mermaManipuleo')
                            ->size('lg')
                            ->weight('bold')
                            ->label('% Manipuleo')
                            ->getStateUsing(function ($record) {

                                $mermaManipuleo = 0;

                                if($record->humedad > 14.5) {
                                    $mermaManipuleo = $manipuleo[$record->cereal] ?? 0;
                                }

                                $mermaManipuleo = [
                                    "Maiz"=>0.25,
                                    "Sorgo"=>0.25,   
                                    "Trigo"=>0.10,
                                    "Cebada"=>0.20,
                                    "Avena"=>0.20,
                                    "Soja"=>0.25,
                                    "Girasol"=>0.20,
                                    "Centeno"=>0.20,
                                    "Triticale"=>0.5,
                                    "Arroz"=>0.13,
                                    "Mijo"=>0.25];

                                return $mermaManipuleo[$record->cereal] . '%';
                            })
                            ->visible(fn ($record) => in_array($record->cereal, ['Maiz', 'Soja', 'Cascara de mani'])),
                        TextEntry::make('calidad')
                            ->size('lg')
                            ->weight('bold')
                            ->label('Calidad')
                            ->formatStateUsing(fn ($state) => (($state == 'muyBuena') ? 'Muy Buena' : ucfirst($state)))
                            ->visible(fn ($record) => in_array($record->cereal, ['Maiz', 'Soja', 'Cascara de mani'])),
                        TextEntry::make('materiasExtranas')
                            ->size('lg')
                            ->weight('bold')
                            ->label('Materias Extrañas %')
                            ->getStateUsing(function ($record) {
                                return $record->materiasExtranas;
                            })
                            ->visible(fn ($record) => in_array($record->cereal, ['Maiz', 'Soja', 'Cascara de mani'])),
                            
                         TextEntry::make('tierra')
                            ->size('lg')
                            ->weight('bold')
                            ->label('Contiene Tierra %')
                            ->visible(fn ($record) => in_array($record->cereal, ['Maiz', 'Soja', 'Cascara de mani'])),
                         TextEntry::make('olor')
                            ->size('lg')
                            ->weight('bold')
                            ->label('Olor %')
                            ->visible(fn ($record) => in_array($record->cereal, ['Maiz', 'Soja', 'Cascara de mani'])),
                        TextEntry::make('pesoNetoHumedad')
                            ->size('lg')
                            ->weight('bold')
                            ->label('Peso Neto por Mermas')
                            ->getStateUsing(function ($record) {

                                    
                                $mermaHumedad = 0;

                                $mermaManipuleo = 0;

                                if($record->humedad > 14.5) {
                                
                                    $mermaHumedad = DB::table('merma_humedad')
                                    ->where('cereal', $record->cereal)
                                    ->where('humedad', $record->humedad)
                                    ->value('merma');
                                
                                    $mermaManipuleo = $manipuleo[$record->cereal] ?? 0;
                                }

                                $manipuleo = [
                                    "Maiz"=>0.25,
                                    "Sorgo"=>0.25,   
                                    "Trigo"=>0.10,
                                    "Cebada"=>0.20,
                                    "Avena"=>0.20,
                                    "Soja"=>0.25,
                                    "Girasol"=>0.20,
                                    "Centeno"=>0.20,
                                    "Triticale"=>0.5,
                                    "Arroz"=>0.13,
                                    "Mijo"=>0.25];

                                $pesoNeto = $record->pesoBruto - $record->pesoTara;
                                $mermaMaterias = ($record->materiasExtranas > 1.5) ? $record->materiasExtranas - 1.5 : 0;
                                $merma = $mermaHumedad + $mermaManipuleo + $mermaMaterias + $record->tierra + $record->olor;
                                $resultado = ($pesoNeto - ($pesoNeto * ($merma / 100)));

                                return number_format($resultado,0,',','.') . ' Kg';
                            })
                            ->visible(fn ($record) => in_array($record->cereal, ['Maiz', 'Soja', 'Cascara de mani'])),
                        TextEntry::make('granosRotos')
                            ->size('lg')
                            ->weight('bold')
                            ->label('Granos Dañados')
                            ->formatStateUsing(fn ($state) => ($state ? 'Sí' : 'No'))
                            ->visible(fn ($record) => in_array($record->cereal, ['Maiz', 'Soja', 'Cascara de mani'])),
                        TextEntry::make('granosQuebrados')
                            ->size('lg')
                            ->weight('bold')
                            ->label('Granos Quebrados')
                            ->formatStateUsing(fn ($state) => ($state ? 'Sí' : 'No'))
                            ->visible(fn ($record) => in_array($record->cereal, ['Maiz', 'Soja', 'Cascara de mani'])),
                        TextEntry::make('destino')
                            ->size('lg')
                            ->weight('bold')
                            ->label('Destino/Almacenamiento')
                            ->formatStateUsing(fn ($state) => ($state == 'plantaSilo' ? 'Planta/Silo' : 'Silo Bolsa'))
                            ->visible(fn ($record) => in_array($record->cereal, ['Maiz', 'Soja', 'Cascara de mani'])),
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
                    ->label('Insumo')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('cartaPorte')
                    ->label('Carta de Porte')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('pesoNeto')
                    ->label('Peso Neto')
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(function ($record) {
                        return number_format($record->pesoBruto - $record->pesoTara, 0, ',', '.') . ' Kg';
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
                Tables\Columns\TextColumn::make('confirmado')
                    ->label('Confirmado')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        return $state
                            ? '<span style="color:green;font-size:1.5em;">&#10003;</span>'
                            : '';
                    })
                    ->html(), // Permite renderizar HTML en la columna
            ])
            ->defaultSort('fecha', 'desc') // Ordenar por la columna 'nombre' de forma ascendente

            ->filters([
               Tables\Filters\Filter::make('fecha')
                    ->form([
                        Forms\Components\DatePicker::make('fecha_desde')->label('Desde'),
                        Forms\Components\DatePicker::make('fecha_hasta')->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['fecha_desde'], fn ($q) => $q->whereDate('fecha', '>=', $data['fecha_desde']))
                            ->when($data['fecha_hasta'], fn ($q) => $q->whereDate('fecha', '<=', $data['fecha_hasta']));
                    }),

                Tables\Filters\SelectFilter::make('cereal')
                    ->label('Insumo')
                            ->options(\App\Models\Insumos::pluck('insumo', 'insumo')->toArray())

            ])
            ->headerActions([
                Tables\Actions\Action::make('download_filtered_pdf')
                    ->label('Reporte PDF Filtrado')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function (Tables\Actions\Action $action) {
                        $filters = $action->getTable()->getFilters();
                        $query = \App\Models\BarloventoCereales::query();
                        $filtro = '';
                        if (!empty($filters['fecha']->getState()['fecha_desde'])) {
                            $query->whereDate('fecha', '>=', $filters['fecha']->getState()['fecha_desde']);
                            $filtro .= 'Desde: '. $filters['fecha']->getState()['fecha_desde'];
                        }
                        if (!empty($filters['fecha']->getState()['fecha_hasta'])) {
                            $query->whereDate('fecha', '<=', $filters['fecha']->getState()['fecha_hasta']);
                            $filtro .= ' - Hasta: '. $filters['fecha']->getState()['fecha_hasta'];
                        }
                        if (!empty($filters['cereal']->getState()['value'])) {
                            $query->where('cereal', $filters['cereal']->getState()['value']);
                            $filtro .= ' Insumo: '. $filters['cereal']->getState()['value'];
                        }
                        $query->orderBy('fecha', 'desc');
                        $records = $query->get();

                        // Construir HTML para el PDF
                            $html = '<table width="100%" style="margin-left:-25px;padding-left:0;">
                                <tr>    
                                    <td style="width="30%" style="text-align:left;">
                                        <img src="images/barlovento-logo.png"/>
                                    </td>
                                    <td style="text-align:center;">
                                        <h2 style="text-align:center;">' . (($filtro == '') ? 'Reporte de Ingreso de Insumos Paihuen' : 'Reporte de Ingreso de Insumos Paihuen - ' . $filtro) . '</h2>
                                    </td>
                                    <td style="text-align:right;">
                                        ' . date('d-m-Y') . '
                                    </td>
                                </tr></table>';
                        $html .= '<table border="1" cellpadding="4" cellspacing="0" width="100%" style="font-size:12px;margin-left:-25;padding-left:0"><thead><tr>';
                        $headers = [
                            'Fecha', 'Insumo', 'C. Porte', 'Vendedor', 'Corredor', 'P.B', 'Tara', 'P.N',
                            '% Humedad', '% Merma', '% Manipuleo', 'Calidad', 'Materias Extrañas', 'Contiene Tierra',
                            'Olor', 'P.N por Mermas', 'Granos Dañados', 'Granos Quebrados', 'Destino', 'Observaciones'
                        ];
                        foreach ($headers as $header) {
                            $html .= '<th style="background:#eee;">' . $header . '</th>';
                        }
                        $html .= '</tr></thead><tbody>';

                        $manipuleo = [
                            "Maiz"=>0.25, "Sorgo"=>0.25, "Trigo"=>0.10, "Cebada"=>0.20, "Avena"=>0.20,
                            "Soja"=>0.25, "Girasol"=>0.20, "Centeno"=>0.20, "Triticale"=>0.5, "Arroz"=>0.13, "Mijo"=>0.25
                        ];

                        $pesoBrutoTotal = 0;
                        $taraTotal = 0;
                        $pesoNetoTotal = 0;
                        $pesoNetoMermasTotal = 0;

                        foreach ($records as $record) {

                            $mermaHumedad = 0;

                            $mermaManipuleo = 0;

                            if($record->humedad > 14.5) {

                                $mermaHumedad = DB::table('merma_humedad')
                                    ->where('cereal', $record->cereal)
                                    ->where('humedad', $record->humedad)
                                    ->value('merma');

                                $mermaManipuleo = $manipuleo[$record->cereal] ?? 0;
                            }

                            $pesoNeto = $record->pesoBruto - $record->pesoTara;
                            $mermaMaterias = ($record->materiasExtranas > 1.5) ? $record->materiasExtranas - 1.5 : 0;
                            $merma = $mermaHumedad + $mermaManipuleo + $mermaMaterias + $record->tierra + $record->olor;
                            $pesoNetoMermas = ($pesoNeto - ($pesoNeto * ($merma / 100)));

                            $html .= '<tr>';
                            $html .= '<td>' . \Carbon\Carbon::parse($record->fecha)->format('d M Y') . '</td>';
                            $html .= '<td>' . $record->cereal . '</td>';
                            $html .= '<td>' . $record->cartaPorte . '</td>';
                            $html .= '<td>' . $record->vendedor . '</td>';
                            $html .= '<td>' . (!is_null($record->corredor) ? $record->corredor : 'No especificado') . '</td>';
                            $html .= '<td>' . number_format($record->pesoBruto, 0, ',', '.') . '</td>';
                            $html .= '<td>' . number_format($record->pesoTara, 0, ',', '.') . '</td>';
                            $html .= '<td>' . number_format($pesoNeto, 0, ',', '.') . '</td>';
                            $html .= '<td style="text-align:center">' . $record->humedad . '</td>';
                            $html .= '<td style="text-align:center">' . $mermaHumedad . '</td>';
                            $html .= '<td style="text-align:center">' . $mermaManipuleo . '</td>';
                            $html .= '<td style="text-align:center">' . $record->calidad . '</td>';
                            $html .= '<td style="text-align:center">' . $record->materiasExtranas . '</td>';
                            $html .= '<td style="text-align:center">' . $record->tierra . '</td>';
                            $html .= '<td style="text-align:center">' . $record->olor . '</td>';
                            $html .= '<td>' . number_format($pesoNetoMermas, 0, ',', '.') . '</td>';
                            $html .= '<td>' . ($record->granosRotos ? 'Sí' : 'No') . '</td>';
                            $html .= '<td>' . ($record->granosQuebrados ? 'Sí' : 'No') . '</td>';
                            $html .= '<td>' . ($record->destino === 'siloBolsa' ? 'Silo Bolsa' : ($record->destino === 'plantaSilo' ? 'Planta de Silo' : $record->destino)) . '</td>';
                            $html .= '<td>' . $record->observaciones . '</td>';
                            $html .= '</tr>';

                            $pesoBrutoTotal += $record->pesoBruto;
                            $taraTotal += $record->pesoTara;
                            $pesoNetoTotal += $pesoNeto;
                            $pesoNetoMermasTotal += $pesoNetoMermas;
                            
                        }

                        $html .= '<tr>
                                    <td colspan="2"><b>TOTALES</b></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>' . number_format($pesoBrutoTotal, 0, ',', '.') . '</td>
                                    <td>' . number_format($taraTotal, 0, ',', '.') . '</td>
                                    <td>' . number_format($pesoNetoTotal, 0, ',', '.') . '</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>' . number_format($pesoNetoMermasTotal, 0, ',', '.') . '</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    </tr>';

                        $html .= '</tbody></table>';

                        // Generar PDF usando Dompdf
                        $pdf = app('dompdf.wrapper');
                        $pdf->loadHTML($html)->setPaper('A4', 'landscape');
                        $filename = 'Reporte_Ingreso_Insumos_Paihuen_' . now()->format('Ymd_His') . '.pdf';
                        // return response($pdf->output(), 200)
                        //     ->header('Content-Type', 'application/pdf')
                        //     ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
                        return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->stream();
                        }, $filename);
                    }),
                Tables\Actions\Action::make('download_filtered_excel')
                    ->label('Reporte Excel Filtrado')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function (Tables\Actions\Action $action) {
                        // Obtener los filtros seleccionados
                        $filters = $action->getTable()->getFilters();

                        // Construir la consulta base
                        $query = \App\Models\PaihuenCereales::query();

                        $filtro = '';
                        // Aplicar filtros manualmente según los valores seleccionados
                        if (!empty($filters['fecha']->getState()['fecha_desde'])) {
                            $query->whereDate('fecha', '>=', $filters['fecha']->getState()['fecha_desde']);
                            $filtro .= 'Desde: '. $filters['fecha']->getState()['fecha_desde'];
                        }
                        if (!empty($filters['fecha']->getState()['fecha_hasta'])) {
                            $query->whereDate('fecha', '<=', $filters['fecha']->getState()['fecha_hasta']);
                            $filtro .= ' - Hasta: '. $filters['fecha']->getState()['fecha_hasta'];

                        }

                        if (!empty($filters['cereal']->getState()['value'])) {
                            $query->where('cereal', $filters['cereal']->getState()['value']);
                            $filtro .= 'Insumo: '. $filters['cereal']->getState()['value'];

                        }

                        $query->orderBy('fecha', 'desc');

                        // Obtener los registros filtrados
                        $records = $query->get();

                        // Crear un nuevo Spreadsheet
                        $spreadsheet = new Spreadsheet();
                        $sheet = $spreadsheet->getActiveSheet();
                        $sheet->mergeCells('A1:I1');
                        $sheet->setCellValue('A1' , ($filtro == '') ? 'Reporte de Ingreso de Insumos Paihuen' : 'Reporte de Ingreso de Insumos Paihuen - ' . $filtro);

                        // Encabezados
                        $headers = [
                            'Fecha',
                            'Insumo',
                            'Carta de Porte',
                            'Vendedor',
                            'Corredor',
                            'Peso Bruto',
                            'Tara',
                            'Peso Neto',
                            '% Humedad',
                            '% Merma de Humedad',
                            '% Manipuleo',
                            'Calidad',
                            'Materias Extrañas',
                            'Contiene Tierra',
                            'Olor',
                            'Peso Neto por Mermas',
                            'Granos Dañados',
                            'Granos Quebrados',
                            'Destino',
                            'Observaciones'
                        ];
                        $sheet->fromArray($headers, null, 'A2');

                        
                        $pesoBrutoTotal = 0;
                        $taraTotal = 0;
                        $pesoNetoTotal = 0;
                        $pesoNetoMermasTotal = 0;

                        // Datos
                        $row = 3;
                        foreach ($records as $record) {
                            $sheet->setCellValue('A' . $row, \Carbon\Carbon::parse($record->fecha)->format('d-m-Y'));
                            $sheet->setCellValue('B' . $row, $record->cereal);
                            $sheet->setCellValue('C' . $row, $record->cartaPorte);
                            $sheet->setCellValue('D' . $row, $record->vendedor);
                            $sheet->setCellValue('E' . $row, (!is_null($record->corredor)) ? $record->corredor : 'No especificado');
                            $sheet->setCellValue('F' . $row, $record->pesoBruto);
                            $sheet->setCellValue('G' . $row, $record->pesoTara);
                            $sheet->setCellValue('H' . $row, $record->pesoBruto - $record->pesoTara);
                            $sheet->setCellValue('I' . $row, $record->humedad);

                             $manipuleo = [
                                    "Maiz"=>0.25,
                                    "Sorgo"=>0.25,   
                                    "Trigo"=>0.10,
                                    "Cebada"=>0.20,
                                    "Avena"=>0.20,
                                    "Soja"=>0.25,
                                    "Girasol"=>0.20,
                                    "Centeno"=>0.20,
                                    "Triticale"=>0.5,
                                    "Arroz"=>0.13,
                                    "Mijo"=>0.25];

                            $mermaHumedad = 0;

                            $mermaManipuleo = 0;

                            if($record->humedad > 14.5) {
                            
                                $mermaHumedad = DB::table('merma_humedad')
                                ->where('cereal', $record->cereal)
                                ->where('humedad', $record->humedad)
                                ->value('merma');
                            
                                $mermaManipuleo = $manipuleo[$record->cereal] ?? 0;
                            }

                            
                            $sheet->setCellValue('J' . $row, $mermaHumedad);
                            $sheet->setCellValue('K' . $row, $mermaManipuleo);
                            $sheet->setCellValue('L' . $row, $record->calidad);
                            $sheet->setCellValue('M' . $row, $record->materiasExtranas);
                            $sheet->setCellValue('N' . $row, $record->tierra);
                            $sheet->setCellValue('O' . $row, $record->olor);

                            $pesoNeto = $record->pesoBruto - $record->pesoTara;
                            $mermaMaterias = ($record->materiasExtranas > 1.5) ? $record->materiasExtranas - 1.5 : 0;
                            $merma = $mermaHumedad + $mermaManipuleo + $mermaMaterias + $record->tierra + $record->olor;
                            $pesoNetoMermas = ($pesoNeto - ($pesoNeto * ($merma / 100)));

                            $sheet->setCellValue('P' . $row, number_format($pesoNetoMermas, 0, '', ''));
                            $sheet->setCellValue('Q' . $row, ($record->granosRotos ? 'Sí' : 'No'));
                            $sheet->setCellValue('R' . $row, ($record->granosQuebrados ? 'Sí' : 'No'));
                            $sheet->setCellValue('S' . $row, $record->destino === 'siloBolsa' ? 'Silo Bolsa' : ($record->destino === 'plantaSilo' ? 'Planta de Silo' : $record->destino));
                            $sheet->setCellValue('T' . $row, $record->observaciones);
                            $row++;

                            $pesoBrutoTotal += $record->pesoBruto;
                            $taraTotal += $record->pesoTara;
                            $pesoNetoTotal += $pesoNeto;
                            $pesoNetoMermasTotal += $pesoNetoMermas;
                            
                        }

                        $sheet->mergeCells('A' . $row . ':B' . $row);

                        $sheet->setCellValue('A' . $row, 'TOTALES');
                        $sheet->setCellValue('F' . $row, $pesoBrutoTotal);
                        $sheet->setCellValue('G' . $row, $taraTotal);
                        $sheet->setCellValue('H' . $row, $pesoNetoTotal);
                        $sheet->setCellValue('P' . $row, $pesoNetoMermasTotal);
                        
                        // Guardar en memoria y devolver como descarga
                        $filename = 'Reporte_Ingreso_Insumos_Paihuen' . now()->format('Ymd_His') . '.xlsx';
                        $tempFile = tempnam(sys_get_temp_dir(), $filename);
                        $writer = new Xlsx($spreadsheet);
                        $writer->save($tempFile);

                        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
                  
                    }),
                Tables\Actions\CreateAction::make()->label('Nuevo Registro'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                ->label('')
                ->color('primary'),
                Tables\Actions\EditAction::make()
                ->label(''),
                Tables\Actions\Action::make('download_infolist_pdf')
                    ->label('Reporte')
                    ->icon('heroicon-o-document')
                    ->color('primary')
                    ->action(function ($record) {
                        $insumo = \App\Models\Insumos::where('insumo', $record->cereal)->first();
                        $pesoNeto = $record->pesoBruto - $record->pesoTara;
                        
                        // Cálculos específicos para cereales
                        $mermaHumedad = 0;
                        $pesoNetoHumedad = $pesoNeto;
                        
                        if (in_array($record->cereal, ['Maiz', 'Soja', 'Cascara de mani'])) {
                            // Calcular merma de humedad según el cereal
                            if ($record->cereal == 'Maiz' && $record->humedad > 14.5) {
                                $mermaHumedad = ($record->humedad - 14.5) * 1.3;
                            } elseif ($record->cereal == 'Soja' && $record->humedad > 13.5) {
                                $mermaHumedad = ($record->humedad - 13.5) * 1.2;
                            } elseif ($record->cereal == 'Cascara de mani' && $record->humedad > 8) {
                                $mermaHumedad = ($record->humedad - 8) * 1.0;
                            }
                            
                            // Calcular peso neto con mermas
                            $totalMermas = $mermaHumedad + ($record->materiasExtranas ?? 0) + ($record->tierra ?? 0) + ($record->olor ?? 0);
                            $pesoNetoHumedad = $pesoNeto - (($pesoNeto * $totalMermas) / 100);
                        }

                        $html = '
                        <style>
                            body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
                            h2 { margin-bottom: 10px; }
                            table { border-collapse: collapse; width: 100%; margin-bottom: 15px;}
                            th, td { border: 1px solid #ccc; padding: 6px 8px; }
                            th { background: #f2f2f2; }
                            .section-title { background: #e9ecef; font-weight: bold; padding: 4px 8px; }
                        </style>';
                

                        for ($i=0; $i < 2; $i++) { 

                                
                            if($record->cereal == 'Maiz' && $i == 1){
                                $html .= '<div style="page-break-after: always;"></div>';
                            }        

                            $html .= '
                            <table width="100%" border="0" cellpadding="5" cellspacing="0" style="margin-bottom:20px;">
                                <tr>
                                    <td style="width:30%;text-align:left;">
                                        <img src="images/barlovento-logo.png" height="30"/>
                                    </td>
                                    <td style="text-align:center;">
                                        <h2 style="margin:0;">Detalle de Ingreso de Insumos Barlovento</h2>
                                    </td>
                                    <td style="text-align:center;">
                                        ' . date('d-m-Y') . '
                                    </td>
                                </tr>
                            </table>
                            <table>
                                <tr><td colspan="5" class="section-title">Información General</td></tr>
                                <tr>
                                    <th>Insumo</th>
                                    <th>Fecha</th>
                                    <th>Carta de Porte</th>
                                    <th>Vendedor</th>
                                    <th>Corredor</th>
                                </tr>
                                <tr>
                                    <td>' . $record->cereal . '</td>
                                    <td>' . \Carbon\Carbon::parse($record->fecha)->format('d-m-Y') . '</td>
                                    <td>' . $record->cartaPorte . '</td>
                                    <td>' . $record->vendedor . '</td>
                                    <td>' . ($record->corredor ?? '-') . '</td>
                                </tr>
                            </table>
                            <table>
                                <tr><td colspan="3" class="section-title">Información de Pesos</td></tr>
                                <tr>
                                    <th>Peso Bruto</th>
                                    <th>Tara</th>
                                    <th>Peso Neto</th>
                                </tr>
                                <tr>
                                    <td>' . number_format($record->pesoBruto, 0, ',', '.') . ' Kg</td>
                                    <td>' . number_format($record->pesoTara, 0, ',', '.') . ' Kg</td>
                                    <td>' . number_format($pesoNeto, 0, ',', '.') . ' Kg</td>
                                </tr>
                            </table>';

                            // Solo mostrar tabla de calidad si es Maiz, Soja o Cascara de mani
                            if (in_array($record->cereal, ['Maiz', 'Soja', 'Cascara de mani'])) {
                                $html .= '
                                <table>
                                    <tr><td colspan="6" class="section-title">Información de Calidad</td></tr>
                                    <tr>
                                        <th>% Humedad</th>
                                        <th>% Merma Humedad</th>
                                        <th>Materias Extrañas %</th>
                                        <th>Tierra %</th>
                                        <th>Olor %</th>
                                        <th>Calidad</th>
                                    </tr>
                                    <tr>
                                        <td>' . number_format($record->humedad, 2, ',', '.') . '%</td>
                                        <td>' . number_format($mermaHumedad, 2, ',', '.') . '%</td>
                                        <td>' . number_format($record->materiasExtranas ?? 0, 2, ',', '.') . '%</td>
                                        <td>' . number_format($record->tierra ?? 0, 2, ',', '.') . '%</td>
                                        <td>' . number_format($record->olor ?? 0, 2, ',', '.') . '%</td>
                                        <td>' . ucfirst($record->calidad) . '</td>
                                    </tr>
                                </table>
                                <table>
                                    <tr><td colspan="4" class="section-title">Información Adicional</td></tr>
                                    <tr>
                                        <th>Granos Dañados</th>
                                        <th>Granos Quebrados</th>
                                        <th>Destino</th>
                                        <th>Peso Neto con Mermas</th>
                                    </tr>
                                    <tr>
                                        <td>' . ($record->granosRotos ? 'Sí' : 'No') . '</td>
                                        <td>' . ($record->granosQuebrados ? 'Sí' : 'No') . '</td>
                                        <td>' . ($record->destino == 'plantaSilo' ? 'Planta de Silo' : 'Silo Bolsa') . '</td>
                                        <td>' . number_format($pesoNetoHumedad, 0, ',', '.') . ' Kg</td>
                                    </tr>
                                </table>';
                            }

                            $html .= '
                            <table>
                                <tr><td colspan="2" class="section-title">Observaciones y Estado</td></tr>
                                <tr>
                                    <th>Observaciones</th>
                                    <th>Estado</th>
                                </tr>
                                <tr>
                                    <td>' . ($record->observaciones ?? '-') . '</td>
                                    <td>' . ($record->confirmado ? 'Confirmado' : 'Pendiente') . '</td>
                                </tr>
                            </table>';

                            if($i == 0)
                                $html .= '<br><hr /><br>';
                        }

                        $pdf = app('dompdf.wrapper');
                        $pdf->loadHTML($html)->setPaper('A4', 'portrait');
                        $filename = 'Detalle_Ingreso_Insumos_Paihuen_' . now()->format('Ymd_His') . '.pdf';
                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->stream();
                        }, $filename);
                    })
                    ->visible(fn ($record) => $record !== null),
                
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
            'index' => Pages\ListPaihuenCereales::route('/'),
            'create' => Pages\CreatePaihuenCereales::route('/create'),
            'edit' => Pages\EditPaihuenCereales::route('/{record}/edit'),
            'view' => Pages\ViewPaihuenCereales::route('/{record}'),
        ];
    }
}
