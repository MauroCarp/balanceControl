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
                Tables\Filters\SelectFilter::make('tipoDestino')
                    ->label('Tipo de Destino')
                    ->options(['FP' => 'Faena Propia', 'VT' => 'Venta a Terceros']),
                Tables\Filters\SelectFilter::make('frigorifico')
                    ->label('Frigorifico')
                    ->options(DestinosEgresos::where('tipo','FRIG')->pluck('nombre', 'id')->toArray()),
                Tables\Filters\SelectFilter::make('destino')
                    ->label('Destino')
                    ->options(function () {
                        // Obtener el valor del filtro 'tipoDestino' desde la sesión o los parámetros
                        $tipoDestino = request()->query('tipoDestino');

                        // Si 'tipoDestino' está presente, filtramos la consulta
                        if ($tipoDestino) {
                            return DestinosEgresos::where('tipo', $tipoDestino)
                                ->pluck('nombre', 'id')
                                ->toArray();
                        }

                        // Si no hay filtro, se devuelven todos los destinos sin filtrar
                        return DestinosEgresos::pluck('nombre', 'id')->toArray();
                    }),                    
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
                                'Peso Desbaste Comercial',
                                'Peso Desbaste Técnico',
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
