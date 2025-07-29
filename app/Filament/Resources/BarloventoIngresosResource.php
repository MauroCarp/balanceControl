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
                        Forms\Components\Grid::make(4)
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
                                    Forms\Components\TextInput::make('productor')
                                            ->label('Productor'),
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
                                                ->label('% Desbaste Comercial')
                                                ->required()
                                                ->id('origen_desbaste')
                                                ->afterStateUpdated(function (callable $set, $state, $get) {
                                                    $set('pesoDesbaste', (float) $get('origen_pesoNeto') - ((float) $get('origen_pesoNeto') * ((float) $get('origen_desbaste') / 100)));
                                                })
                                                ->helperText('Si tiene duda con el desbaste, consultar al comercial.'),
                                            Forms\Components\TextInput::make('pesoDesbaste')
                                                ->label('Peso Neto Desbaste Comercial')
                                                ->disabled()
                                                ->id('pesoDesbaste')
                                                ->default(0)
                                                ->dehydrated(false)
                                                ->reactive(),
                                            Forms\Components\TextInput::make('pesoDesbasteTecnico')
                                                ->label('Peso Neto Desbaste Tecnico')
                                                ->disabled()
                                                ->id('pesoDesbasteTecnico')
                                                ->default(0)
                                                ->dehydrated(false)
                                                ->reactive(),
                                            ]),
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
                        Forms\Components\Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->maxLength(400)
                            ->rows(1),
                        
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
                Tables\Columns\TextColumn::make('origen_pesoNeto')
                    ->label('Peso Neto Origen')
                    ->sortable()
                    ->searchable()
                     ->formatStateUsing(function ($state) {
                        return number_format($state, 0, ',', '.') . ' Kg';
                    }),
                Tables\Columns\TextColumn::make('origen_terneros')
                    ->label('Terneros Origen')
                    ->sortable(),
                Tables\Columns\TextColumn::make('origen_terneras')
                    ->label('Terneras Origen')
                    ->sortable(),
                Tables\Columns\TextColumn::make('dte')
                    ->label('Nº DTE')
                    ->sortable()
                    ->searchable(),
            ])
            ->defaultSort('fecha', 'desc') // Ordenar por la columna 'nombre' de forma ascendente

            ->filters([
                Tables\Filters\Filter::make('fecha')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q, $date) => $q->whereDate('fecha', '>=', $date))
                            ->when($data['until'], fn ($q, $date) => $q->whereDate('fecha', '<=', $date));
                    }),
                Tables\Filters\SelectFilter::make('consignatario')
                    ->label('Consignatario')
                    ->options(Consignatarios::pluck('nombre', 'id')->toArray()),
            ])
            ->headerActions([
                Tables\Actions\Action::make('download_filtered_pdf')
                    ->label('Reporte PDF Filtrado')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('danger')
                    ->action(function (Tables\Actions\Action $action) {

                        $filters = $action->getTable()->getFilters();
                        // dump($filters);
                        $html = '';
                        $query = \App\Models\BarloventoIngresos::query();
                        $filtro = '';

                        if (!empty($filters['fecha']->getState()['from'])) {
                            $query->whereDate('fecha', '>=', $filters['fecha']->getState()['from']);
                            $filtro .= 'Desde: '. $filters['fecha']->getState()['from'];
                        }

                        if (!empty($filters['fecha']->getState()['until'])) {
                            $query->whereDate('fecha', '<=', $filters['fecha']->getState()['until']);
                            $filtro .= ' - Hasta: '. $filters['fecha']->getState()['until'];
                        }
                        if (!empty($filters['consignatario']->getState()['value'])) {
                            $query->where('cereal', $filters['cereal']->getState()['value']);
                            $filtro .= ' Insumo: '. $filters['cereal']->getState()['value'];
                        }
                        $query->orderBy('fecha', 'desc');
                        $records = $query->get();

                        $html .= '<table width="100%" border="0" cellpadding="5" cellspacing="0" style="margin-bottom:20px;">
                            <tr>
                                <td style="width:30%;text-align:left;">
                                    <img src="images/barlovento-logo.png" height="40"/>
                                </td>
                                <td style="text-align:center;">
                                    <h2 style="margin:0;">' . (($filtro == '') ? 'Reporte de Ingreso de Animales Barlovento' : 'Reporte de Ingreso de Animales Barlovento - ' . $filtro) . '</h2>
                                </td>
                                <td style="text-align:right;">
                                    ' . date('d-m-Y') . '
                                </td>
                            </tr>
                        </table>';

                        // Nueva tabla: una sola tabla con todos los registros, cada fila un registro y cada columna un campo
                        if ($records->count() > 0) {
                            // Encabezados de la tabla
                            $html .= '<table border="1" cellpadding="4" cellspacing="0" width="100%" style="margin-bottom:20px;">';
                            $html .= '<thead style="background:#f2f2f2;font-size:12px"><tr>';
                            $headers = [
                                'Fecha',
                                'Consignatario',
                                'Total Hacienda Origen',
                                'Peso Neto Origen',
                                'Peso Neto Desbaste Comercial',
                                'Peso Neto Desbaste Técnico',
                                'Peso Neto Destino',
                                'Dif. PN Desbaste Técnico - PN Destino',
                                'Observaciones',
                                'Precio Kg',
                                '$ Total Neto',
                                '$ Total c/IVA',
                                '% Comisión',
                                '$ IVA Comisión',
                                'Flete',
                                '$ Flete',
                                'Otros Gastos',
                                'Total c/IVA a Pagar',
                                '$ Neto de compra por Kg'
                            ];
                            foreach ($headers as $header) {
                                $html .= '<th>' . $header . '</th>';
                            }
                            $html .= '</tr></thead><tbody style="font-size:9px">';

                            foreach ($records as $record) {
                                $consignatario = \App\Models\Consignatarios::find($record->consignatario);
                                $pesoDesbasteComercial = $record->origen_pesoNeto - ($record->origen_pesoNeto * ($record->origen_desbaste / 100));
                                $porcentajeRestar = 0;
                                if ($record->origen_distancia < 300) {
                                    $porcentajeRestar = 1.5 + (floor($record->origen_distancia / 100) * 0.5);
                                } else {
                                    $porcentajeRestar = floor($record->origen_distancia / 100) * 1 + (($record->origen_distancia % 100) / 100 * 1);
                                }
                                $pesoDesbasteTecnico = $record->origen_pesoNeto - (($record->origen_pesoNeto * $porcentajeRestar) / 100);

                                $pesoNetoDestino = $record->destino_pesoBruto - $record->destino_tara;
                                $diferenciaTecnicoDestino = $pesoDesbasteTecnico - $pesoNetoDestino;
                                $alerta = $diferenciaTecnicoDestino > 0
                                    ? '<span style="color:red;">Alerta</span>'
                                    : '';

                                $costoTotal = $record->precioKg * $pesoDesbasteComercial;
                                $totalConIva = $costoTotal + (($costoTotal * 10.5) / 100);
                                $comisionPorc = $consignatario?->porcentajeComision ?? 0;
                                $totalComision = ($costoTotal * $comisionPorc) / 100;
                                $ivaComision = ($totalComision * 10.5) / 100;
                                $totalConIvaApagar = $totalConIva + $totalComision + $record->precioFlete + $record->precioOtrosGastos;
                                $precioNetoKg = $pesoDesbasteComercial > 0
                                    ? ($record->precioKg + ($totalComision / $pesoDesbasteComercial) + ($record->precioFlete / $pesoDesbasteComercial) + ($record->precioOtrosGastos / $pesoDesbasteComercial))
                                    : 0;

                                $html .= '<tr>';
                                $html .= '<td>' . \Carbon\Carbon::parse($record->fecha)->format('d-M-Y') . '</td>';
                                $html .= '<td>' . ($consignatario?->nombre ?? '-') . '</td>';
                                $html .= '<td>' . ($record->origen_terneros + $record->origen_terneras) . '</td>';
                                $html .= '<td>' . number_format($record->origen_pesoNeto, 0, ',', '.') . ' Kg</td>';
                                $html .= '<td>' . number_format($pesoDesbasteComercial, 0, ',', '.') . ' Kg</td>';
                                $html .= '<td>' . number_format($pesoDesbasteTecnico, 0, ',', '.') . ' Kg</td>';
                                $html .= '<td>' . number_format($pesoNetoDestino, 0, ',', '.') . ' Kg</td>';
                                $html .= '<td>' . number_format($diferenciaTecnicoDestino, 0, ',', '.') . ' Kg ' . $alerta . '</td>';
                                $html .= '<td>' . ($record->observaciones ?? '-') . '</td>';
                                $html .= '<td>$ ' . number_format($record->precioKg, 2, ',', '.') . '</td>';
                                $html .= '<td>$ ' . number_format($costoTotal, 2, ',', '.') . '</td>';
                                $html .= '<td>$ ' . number_format($totalConIva, 2, ',', '.') . '</td>';
                                $html .= '<td>' . $comisionPorc . '% - $ ' . number_format($totalComision, 2, ',', '.') . '</td>';
                                $html .= '<td>$ ' . number_format($ivaComision, 2, ',', '.') . '</td>';
                                $html .= '<td>' . ($record->precioFlete != 0 ? 'SI' : 'NO') . '</td>';
                                $html .= '<td>$ ' . number_format($record->precioFlete, 2, ',', '.') . '</td>';
                                $html .= '<td>$ ' . number_format($record->precioOtrosGastos, 2, ',', '.') . '</td>';
                                $html .= '<td>$ ' . number_format($totalConIvaApagar, 2, ',', '.') . '</td>';
                                $html .= '<td>$ ' . number_format($precioNetoKg, 2, ',', '.') . '</td>';
                                $html .= '</tr>';
                            }
                            $html .= '</tbody></table>';
                        } else {
                            $html .= '<p>No hay registros para mostrar.</p>';
                        }
                    
                        // Generar PDF usando Dompdf
                        $pdf = app('dompdf.wrapper');
                        $pdf->loadHTML($html)->setPaper('A4', 'landscape');
                        $filename = 'Reporte_Ingreso_Insumos_Barlovento_' . now()->format('Ymd_His') . '.pdf';
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
                    ->color('success')
                    ->action(function (Tables\Actions\Action $action) {
                        // Obtener los filtros seleccionados
                        $filters = $action->getTable()->getFilters();

                        // Construir la consulta base
                        $query = \App\Models\BarloventoIngresos::query();

                        $filtro = '';
                        // Aplicar filtros manualmente según los valores seleccionados
                        if (!empty($filters['fecha']->getState()['from'])) {
                            $query->whereDate('fecha', '>=', $filters['fecha']->getState()['from']);
                            $filtro .= 'Desde: '. $filters['fecha']->getState()['from'];
                        }
                        if (!empty($filters['fecha']->getState()['until'])) {
                            $query->whereDate('fecha', '<=', $filters['fecha']->getState()['until']);
                            $filtro .= ' - Hasta: '. $filters['fecha']->getState()['until'];
                        }
                        if (!empty($filters['consignatario']->getState()['value'])) {
                            $query->where('consignatario', $filters['consignatario']->getState()['value']);
                            $consignatarioNombre = \App\Models\Consignatarios::find($filters['consignatario']->getState()['value'])?->nombre ?? '';
                            $filtro .= ' Consignatario: '. $consignatarioNombre;
                        }

                        $query->orderBy('fecha', 'desc');

                        // Obtener los registros filtrados
                        $records = $query->get();

                        // Crear un nuevo Spreadsheet
                        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                        $sheet = $spreadsheet->getActiveSheet();
                        $sheet->mergeCells('A1:AD1');
                        $sheet->setCellValue('A1', ($filtro == '') ? 'Reporte de Ingreso de Animales Barlovento' : 'Reporte de Ingreso de Animales Barlovento - ' . $filtro);

                        // Encabezados (igual que PDF)
                        $headers = [
                            'Fecha',
                            'Consignatario',
                            'N° DTE',
                            'Terneros Origen',
                            'Terneras Origen',
                            'Total Hacienda Origen',
                            'Peso Bruto Origen',
                            'Peso Neto Origen',
                            'Tara Origen',
                            'Distancia Recorrida',
                            '% Desbaste',
                            'Peso Desbaste Comercial',
                            'Peso Desbaste Técnico',
                            'Terneros Destino',
                            'Terneras Destino',
                            'Total Hacienda Destino',
                            'Peso Bruto Destino',
                            'Tara Destino',
                            'Peso Neto Destino',
                            'Dif. PN Desbaste Técnico - PN Destino',
                            'Observaciones',
                            'Precio Kg',
                            '$ Total Neto',
                            '$ Total c/IVA',
                            '% Comisión',
                            '$ IVA Comisión',
                            'Flete',
                            '$ Flete',
                            'Otros Gastos',
                            'Total c/IVA a Pagar',
                            '$ Neto de compra por Kg'
                        ];
                        // Escribir los encabezados en la fila 2
                        $sheet->fromArray($headers, null, 'A2');

                        // Aplicar formato: fondo y negrita a los encabezados
                        $headerStyle = [
                            'font' => [
                                'bold' => true,
                            ],
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => [
                                    'rgb' => 'D9E1F2', // color celeste claro, puedes cambiarlo
                                ],
                            ],
                        ];
                        $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
                        $sheet->getStyle("A2:{$lastColumn}2")->applyFromArray($headerStyle);
                 
                        $totalTernerosOrigen = 0;
                        $totalTernerasOrigen = 0;
                        $totalHaciendaOrigen = 0;
                        $totalPesoBrutoOrigen = 0;
                        $totalPesoNetoOrigen = 0;
                        $totalTaraOrigen = 0;
                        $totalPesoDesbasteComercial = 0;
                        $totalPesoDesbasteTecnico = 0;
                        $totalTernerosDestino = 0;
                        $totalTernerasDestino = 0;
                        $totalHaciendaDestino = 0;
                        $totalPesoBrutoDestino = 0;
                        $totalTaraDestino = 0;
                        $totalPesoNetoDestino = 0;
                        $totalPrecioKg = 0;
                        $totalNeto = 0;
                        $totalConIva = 0;
                        $totalIvaComision = 0;
                        $totalFlete = 0;
                        $totalOtrosGastos = 0;
                        $totalConIvaApagar = 0;
                        $totalNetoCompraPorKg = 0;
                        $totalDistancia = 0;
                        $totalDesbaste = 0;
                        $totalRows = 0;

                        $row = 3;
                        foreach ($records as $record) {
                            $consignatario = \App\Models\Consignatarios::find($record->consignatario);
                            $pesoDesbasteComercial = $record->origen_pesoNeto - ($record->origen_pesoNeto * ($record->origen_desbaste / 100));
                            $porcentajeRestar = 0;
                            if ($record->origen_distancia < 300) {
                                $porcentajeRestar = 1.5 + (floor($record->origen_distancia / 100) * 0.5);
                            } else {
                                $porcentajeRestar = floor($record->origen_distancia / 100) * 1 + (($record->origen_distancia % 100) / 100 * 1);
                            }
                            $pesoDesbasteTecnico = $record->origen_pesoNeto - (($record->origen_pesoNeto * $porcentajeRestar) / 100);

                            $pesoNetoDestino = $record->destino_pesoBruto - $record->destino_tara;
                            $diferenciaTecnicoDestino = $pesoDesbasteTecnico - $pesoNetoDestino;

                            $costoTotal = $record->precioKg * $pesoDesbasteComercial;
                            $totalConIva = $costoTotal + (($costoTotal * 10.5) / 100);
                            $comisionPorc = $consignatario?->porcentajeComision ?? 0;
                            $totalComision = ($costoTotal * $comisionPorc) / 100;
                            $ivaComision = ($totalComision * 10.5) / 100;
                            $totalConIvaApagar = $totalConIva + $totalComision + $record->precioFlete + $record->precioOtrosGastos;
                            $precioNetoKg = $pesoDesbasteComercial > 0
                                ? ($record->precioKg + ($totalComision / $pesoDesbasteComercial) + ($record->precioFlete / $pesoDesbasteComercial) + ($record->precioOtrosGastos / $pesoDesbasteComercial))
                                : 0;

                            $sheet->setCellValue('A' . $row, \Carbon\Carbon::parse($record->fecha)->format('d-m-Y'));
                            $sheet->setCellValue('B' . $row, $consignatario?->nombre ?? '-');
                            $sheet->setCellValue('C' . $row, $record->dte);
                            $sheet->setCellValue('D' . $row, $record->origen_terneros);
                            $sheet->setCellValue('E' . $row, $record->origen_terneras);
                            $sheet->setCellValue('F' . $row, $record->origen_terneros + $record->origen_terneras);
                            $sheet->setCellValue('G' . $row, $record->origen_pesoBruto);
                            $sheet->setCellValue('H' . $row, $record->origen_pesoNeto);
                            $sheet->setCellValue('I' . $row, $record->origen_pesoBruto - $record->origen_pesoNeto);
                            $sheet->setCellValue('J' . $row, $record->origen_distancia);
                            $sheet->setCellValue('K' . $row, $record->origen_desbaste);
                            $sheet->setCellValue('L' . $row, $pesoDesbasteComercial);
                            $sheet->setCellValue('M' . $row, $pesoDesbasteTecnico);
                            $sheet->setCellValue('N' . $row, $record->destino_terneros);
                            $sheet->setCellValue('O' . $row, $record->destino_terneras);
                            $sheet->setCellValue('P' . $row, $record->destino_terneros + $record->destino_terneras);
                            $sheet->setCellValue('Q' . $row, $record->destino_pesoBruto);
                            $sheet->setCellValue('R' . $row, $record->destino_tara);
                            $sheet->setCellValue('S' . $row, $pesoNetoDestino);
                            $sheet->setCellValue('T' . $row, $diferenciaTecnicoDestino);
                            $sheet->setCellValue('U' . $row, $record->observaciones ?? '-');
                            $sheet->setCellValue('V' . $row, $record->precioKg);
                            $sheet->setCellValue('W' . $row, $costoTotal);
                            $sheet->setCellValue('X' . $row, $totalConIva);
                            $sheet->setCellValue('Y' . $row, $comisionPorc . '% - $' . $totalComision);
                            $sheet->setCellValue('Z' . $row, $ivaComision);
                            $sheet->setCellValue('AA' . $row, ($record->precioFlete != 0 ? 'SI' : 'NO'));
                            $sheet->setCellValue('AB' . $row, $record->precioFlete);
                            $sheet->setCellValue('AC' . $row, $record->precioOtrosGastos);
                            $sheet->setCellValue('AD' . $row, $totalConIvaApagar);
                            $sheet->setCellValue('AE' . $row, $precioNetoKg);

                            $row++;
                            $totalDistancia += $record->origen_distancia;
                            $totalTernerosOrigen += $record->origen_terneros;
                            $totalTernerasOrigen += $record->origen_terneras;
                            $totalHaciendaOrigen += ($record->origen_terneros + $record->origen_terneras);
                            $totalPesoBrutoOrigen += $record->origen_pesoBruto;
                            $totalPesoNetoOrigen += $record->origen_pesoNeto;
                            $totalTaraOrigen += ($record->origen_pesoBruto - $record->origen_pesoNeto);
                            $totalPesoDesbasteComercial += $pesoDesbasteComercial;
                            $totalPesoDesbasteTecnico += $pesoDesbasteTecnico;
                            $totalTernerosDestino += $record->destino_terneros;
                            $totalTernerasDestino += $record->destino_terneras;
                            $totalHaciendaDestino += ($record->destino_terneros + $record->destino_terneras);
                            $totalPesoBrutoDestino += $record->destino_pesoBruto;
                            $totalTaraDestino += $record->destino_tara;
                            $totalPesoNetoDestino += $pesoNetoDestino;
                            $totalPrecioKg += $record->precioKg;
                            $totalNeto += $costoTotal;
                            $totalConIva += $totalConIva;
                            $totalIvaComision += $ivaComision;
                            $totalFlete += $record->precioFlete;
                            $totalOtrosGastos += $record->precioOtrosGastos;
                            $totalConIvaApagar += $totalConIvaApagar;
                            $totalNetoCompraPorKg += $precioNetoKg;
                            $totalDesbaste += $record->origen_desbaste;

                            $totalRows++;
                        }

                        // Escribir la fila de totales
                        $sheet->setCellValue('A' . $row, 'TOTALES');
                        $sheet->setCellValue('D' . $row, $totalTernerosOrigen);
                        $sheet->setCellValue('E' . $row, $totalTernerasOrigen);
                        $sheet->setCellValue('F' . $row, $totalHaciendaOrigen);
                        $sheet->setCellValue('G' . $row, $totalPesoBrutoOrigen);
                        $sheet->setCellValue('H' . $row, $totalPesoNetoOrigen);
                        $sheet->setCellValue('I' . $row, $totalTaraOrigen);
                        $sheet->setCellValue('L' . $row, $totalPesoDesbasteComercial);
                        $sheet->setCellValue('M' . $row, $totalPesoDesbasteTecnico);
                        $sheet->setCellValue('N' . $row, $totalTernerosDestino);
                        $sheet->setCellValue('O' . $row, $totalTernerasDestino);
                        $sheet->setCellValue('P' . $row, $totalHaciendaDestino);
                        $sheet->setCellValue('Q' . $row, $totalPesoBrutoDestino);
                        $sheet->setCellValue('R' . $row, $totalTaraDestino);
                        $sheet->setCellValue('S' . $row, $totalPesoNetoDestino);
                        $sheet->setCellValue('V' . $row, $totalPrecioKg);
                        $sheet->setCellValue('W' . $row, $totalNeto);
                        $sheet->setCellValue('X' . $row, $totalConIva);
                        $sheet->setCellValue('Z' . $row, $totalIvaComision);
                        $sheet->setCellValue('AB' . $row, $totalFlete);
                        $sheet->setCellValue('AC' . $row, $totalOtrosGastos);
                        $sheet->setCellValue('AD' . $row, $totalConIvaApagar);
                        $sheet->setCellValue('AE' . $row, $totalNetoCompraPorKg);

                        $sheet->getStyle("A{$row}:AE{$row}")->getFont()->setBold(true);
                        $totalsStyle = [
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => [
                                    'rgb' => 'F9F9C5', // amarillo claro
                                ],
                            ],
                        ];
                        $sheet->getStyle("A{$row}:AE{$row}")->applyFromArray($totalsStyle);
                        $filename = 'Reporte_Ingreso_Animales_Barlovento_' . now()->format('Ymd_His') . '.xlsx';
                        $tempFile = tempnam(sys_get_temp_dir(), $filename);
                        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                        $writer->save($tempFile);

                        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
                    }),
                Tables\Actions\CreateAction::make()->label('Nuevo Registro'),
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
                GridInfolist::make(3)
                    ->schema([
                        TextEntry::make('fecha')
                            ->label('Fecha')
                            ->size('lg')
                            ->weight('bold')
                            ->getStateUsing(function ($record) {
                                return \Carbon\Carbon::parse($record->fecha)->format('d-m-Y');
                            }),
                        TextEntry::make('consignatario')
                            ->label('Consignatario')
                            ->size('lg')
                            ->weight('bold')
                            ->getStateUsing(function ($record) {
                                return Consignatarios::find($record->consignatario)?->nombre ?? '-';
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
                                        ->label('Tara')
                                        ->size('lg')
                                        ->weight('bold')
                                        ->getStateUsing(function ($record) {
                                            return number_format(($record->origen_pesoBruto - $record->origen_pesoNeto), 0, ',', '.') . ' Kg';
                                        }),
                                ]),
                                GridInfolist::make(4)
                                    ->schema([
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
                                        ->label('Peso Desbaste Comercial')
                                        ->size('lg')
                                        ->weight('bold')
                                        ->getStateUsing(function ($record) {
                                            return number_format(($record->origen_pesoNeto - ($record->origen_pesoNeto * ($record->origen_desbaste / 100))), 0, ',', '.') . ' Kg';
                                        }),
                                    TextEntry::make('origen_pesoDesbasteTecnico')
                                        ->label('Peso Desbaste Tecnico')
                                        ->size('lg')
                                        ->weight('bold')
                                        ->getStateUsing(function ($record) {

                                            $porcentajeRestar = 0;

                                            if ($record->origen_distancia < 300) {
                                                $porcentajeRestar = 1.5 + (floor($record->origen_distancia / 100) * 0.5);
                                            } else {
                                                $porcentajeRestar = floor($record->origen_distancia / 100) * 1 + (($record->origen_distancia % 100) / 100 * 1);
                                            }

                                            $nuevoPesoNeto = $record->origen_pesoNeto - (($record->origen_pesoNeto * $porcentajeRestar) / 100);
                                            return number_format($nuevoPesoNeto, 0, ',', '.') . ' Kg';
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
                                            return number_format($record->destino_pesoBruto, 0, ',', '.') . ' Kg';
                                        }),
                                    TextEntry::make('destino_tara')
                                        ->label('Tara')
                                        ->size('lg')
                                        ->weight('bold')
                                        ->getStateUsing(function ($record) {
                                            return number_format($record->destino_tara, 0, ',', '.') . ' Kg';
                                        }),
                                    TextEntry::make('destino_diferencia')
                                        ->label('Peso Neto')
                                        ->size('lg')
                                        ->weight('bold')
                                        ->getStateUsing(function ($record) {
                                            return number_format(($record->destino_pesoBruto - $record->destino_tara), 0, ',', '.') . ' Kg';
                                        }),
                                    TextEntry::make('diferencia_PNDesbasteTecnico_PNDestino')
                                        ->label('Dif. Peso Neto con Desbaste Técnico - Peso Neto Destino')
                                        ->size('lg')
                                        ->weight('bold')
                                        ->getStateUsing(function ($record) {
                                           
                                            $porcentajeRestar = 0;

                                            if ($record->origen_distancia < 300) {
                                                $porcentajeRestar = 1.5 + (floor($record->origen_distancia / 100) * 0.5);
                                            } else {
                                                $porcentajeRestar = floor($record->origen_distancia / 100) * 1 + (($record->origen_distancia % 100) / 100 * 1);
                                            }

                                            $pesoNetoDesbasteTecnico = $record->origen_pesoNeto - (($record->origen_pesoNeto * $porcentajeRestar) / 100);

                                            $pesoNetoDestino = $record->destino_pesoBruto - $record->destino_tara;

                                            $diferencia = $pesoNetoDesbasteTecnico - $pesoNetoDestino;

                                            if($pesoNetoDesbasteTecnico > $pesoNetoDestino) {
                                                return number_format($diferencia, 0, ',', '.') . ' Kg <span style="color:red;">&#128078; Alerta</span>';
                                            } else {
                                                return number_format($diferencia, 0, ',', '.') . ' Kg <span style="color:green;">&#128077;</span>';
                                            }

                                        })
                                        ->html(),
                                    TextEntry::make('observaciones')
                                        ->label('Observaciones')
                                        ->size('lg')
                                        ->weight('bold'),
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

                                            $pesoNetoDesbasteComercial = $record->origen_pesoNeto - ($record->origen_pesoNeto * ($record->origen_desbaste / 100));

                                            return '$ ' . number_format(($record->precioKg * $pesoNetoDesbasteComercial), 2, ',', '.');
                                        }),
                                        TextEntry::make('iva')
                                            ->label('$ Total c/IVA')
                                            ->size('lg')
                                            ->weight('bold')
                                            ->getStateUsing(function ($record) {
                                                $pesoNetoDesbasteComercial = $record->origen_pesoNeto - ($record->origen_pesoNeto * ($record->origen_desbaste / 100));
    
                                                $costoTotal = $record->precioKg * $pesoNetoDesbasteComercial;
                                                $totalConIva = $costoTotal + (($costoTotal * 10.5) /100);
                                                return '$ ' . number_format($totalConIva, 2, ',', '.');
                                            }),
                                    GridInfolist::make(4)
                                    ->schema([
                                        TextEntry::make('comision')
                                            ->label('% Comisión')
                                            ->size('lg')
                                            ->weight('bold')
                                            ->getStateUsing(function ($record) {
                                                $comision = Consignatarios::find($record->consignatario)?->porcentajeComision ?? 0;
                                                $pesoNetoDesbasteComercial = $record->origen_pesoNeto - ($record->origen_pesoNeto * ($record->origen_desbaste / 100));
                                                
                                                $costoTotal = $record->precioKg * $pesoNetoDesbasteComercial;
                                                // $totalConIva = $costoTotal + (($costoTotal * 10.5) /100);

                                                return $comision . '% - $ ' . number_format((($costoTotal * $comision) / 100),2,',','.');
                                            }),
                                        TextEntry::make('ivaComision')
                                            ->label('$ IVA Comisión')
                                            ->size('lg')
                                            ->weight('bold')
                                            ->getStateUsing(function ($record) {
                                                $comision = Consignatarios::find($record->consignatario)?->porcentajeComision ?? 0;
                                                $pesoNetoDesbasteComercial = $record->origen_pesoNeto - ($record->origen_pesoNeto * ($record->origen_desbaste / 100));
                                                
                                                $costoTotal = $record->precioKg * $pesoNetoDesbasteComercial;
                                                $totalComision = ($costoTotal * $comision) / 100;

                                                $iva = ($totalComision * 10.5) /100;

                                                return '$ ' . number_format($iva,2,',','.');
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
                                    ]),
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
                                            $comision = Consignatarios::find($record->consignatario)?->porcentajeComision ?? 0;
                                            $pesoNetoDesbasteComercial = $record->origen_pesoNeto - ($record->origen_pesoNeto * ($record->origen_desbaste / 100));

                                            $costoTotal = $record->precioKg * $pesoNetoDesbasteComercial;
                                            $totalConIva = $costoTotal + (($costoTotal * 10.5) /100);


                                            return '$ ' . number_format(($totalConIva + (($totalConIva * $comision) / 100) + $record->precioFlete + $record->precioOtrosGastos), 2, ',', '.');
                                        }),

                                    TextEntry::make('precioNetoKg')
                                        ->label('$ Neto de compra por Kg')
                                        ->size('lg')
                                        ->weight('bold')
                                        ->getStateUsing(function ($record) {
                                            $comision = Consignatarios::find($record->consignatario)?->porcentajeComision ?? 0;
                                            $pesoNetoDesbasteComercial = $record->origen_pesoNeto - ($record->origen_pesoNeto * ($record->origen_desbaste / 100));

                                            $costoTotal = $record->precioKg * $pesoNetoDesbasteComercial;

                                            return '$ ' . number_format((($record->precioKg) + ((($costoTotal * $comision) / 100) / $pesoNetoDesbasteComercial) + ($record->precioFlete / $pesoNetoDesbasteComercial) + ($record->precioOtrosGastos / $pesoNetoDesbasteComercial)), 2, ',', '.');
                                           
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
