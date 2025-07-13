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
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid as GridInfolist;
use Filament\Infolists\Components\Group;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
                            $html = '<table width="100%">
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
                        $html .= '<table border="1" cellpadding="4" cellspacing="0" width="100%" style="font-size:12px;"><thead><tr>';
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

                        foreach ($records as $record) {
                            $mermaHumedad = DB::table('merma_humedad')
                                ->where('cereal', $record->cereal)
                                ->where('humedad', $record->humedad)
                                ->value('merma');
                            $mermaManipuleo = $manipuleo[$record->cereal] ?? 0;
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
                        }
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
                    ->label('Reporte Filtrado')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function (Tables\Actions\Action $action) {
                        // Obtener los filtros seleccionados
                        $filters = $action->getTable()->getFilters();
                        
                        // Construir la consulta base
                        $query = \App\Models\PaihuenEgresoCereales::query();

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
                        $sheet->mergeCells('A1:F1');
                            
                        $sheet->setCellValue('A1' , ($filtro == '') ? 'Reporte de Egreso de Insumos Paihuen' : 'Reporte de Egreso de Insumos Paihuen - ' . $filtro);

                        // Encabezados
                        $headers = [
                            'Fecha',
                            'Insumo',
                            'Carta de Porte',
                            'Observaciones',
                            'Peso Bruto',
                            'Tara',
                            'Peso Neto',
                        ];
                        $sheet->fromArray($headers, null, 'A2');

                        // Datos
                        $row = 3;
                        foreach ($records as $record) {
                            $sheet->setCellValue('A' . $row, \Carbon\Carbon::parse($record->fecha)->format('d-m-Y'));
                            $sheet->setCellValue('B' . $row, $record->cereal);
                            $sheet->setCellValue('C' . $row, $record->cartaPorte);
                            $sheet->setCellValue('D' . $row, $record->observaciones);
                            $sheet->setCellValue('E' . $row, $record->pesoBruto);
                            $sheet->setCellValue('F' . $row, $record->pesoTara);
                            $sheet->setCellValue('G' . $row, $record->pesoBruto - $record->pesoTara);
                            $row++;
                        }

                        // Guardar en memoria y devolver como descarga
                        $filename = 'Reporte_Egreso_Insumos_Paihuen' . now()->format('Ymd_His') . '.xlsx';
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
