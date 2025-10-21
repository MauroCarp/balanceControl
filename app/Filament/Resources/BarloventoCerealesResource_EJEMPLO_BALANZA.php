<?php
/**
 * EJEMPLO DE INTEGRACIÓN DE BALANZA EN FORMULARIO FILAMENT
 * 
 * Este es un ejemplo de cómo modificar tu BarloventoCerealesResource
 * para agregar botones de lectura de balanza.
 * 
 * Copia y adapta las secciones relevantes a tu Resource.
 */

namespace App\Filament\Resources;

use App\Filament\Resources\BarloventoCerealesResource\Pages;
use App\Models\BarloventoCereales;
use App\Models\Insumos;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;

class BarloventoCerealesResourceEjemplo extends Resource
{
    protected static ?string $model = BarloventoCereales::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Ingreso')
                    ->schema([
                        Forms\Components\Grid::make(5)
                            ->schema([
                                Forms\Components\Select::make('cereal')
                                    ->label('Insumo')
                                    ->options(\App\Models\Insumos::pluck('insumo', 'insumo')->toArray())
                                    ->required()
                                    ->searchable(),
                                
                                Forms\Components\DatePicker::make('fecha')
                                    ->label('Fecha')
                                    ->required(),
                                
                                Forms\Components\TextInput::make('cartaPorte')
                                    ->label('Carta de Porte')
                                    ->required(),
                                
                                Forms\Components\TextInput::make('vendedor')
                                    ->label('Vendedor')
                                    ->required(),
                                
                                Forms\Components\TextInput::make('corredor')
                                    ->label('Corredor'),
                        ]),
                    ]),

                // SECCIÓN DE PESAJE CON INTEGRACIÓN DE BALANZA
                Forms\Components\Section::make('Pesaje con Balanza Digital')
                    ->description('Use los botones para leer automáticamente los valores de la balanza')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                // PESO BRUTO CON BOTÓN DE LECTURA
                                Forms\Components\Grid::make(1)
                                    ->schema([
                                        Forms\Components\TextInput::make('pesoBruto')
                                            ->id('pesoBruto')
                                            ->label('Peso Bruto (kg)')
                                            ->required()
                                            ->numeric()
                                            ->suffix('kg')
                                            ->helperText('Presione el botón abajo para leer de la balanza'),
                                        
                                        // Botón para leer peso bruto
                                        Forms\Components\View::make('components.balanza-button')
                                            ->viewData([
                                                'targetField' => 'pesoBruto',
                                                'buttonText' => '📊 Leer Peso Bruto',
                                            ]),
                                    ]),

                                // TARA CON BOTÓN DE LECTURA
                                Forms\Components\Grid::make(1)
                                    ->schema([
                                        Forms\Components\TextInput::make('pesoTara')
                                            ->id('pesoTara')
                                            ->label('Tara (kg)')
                                            ->required()
                                            ->numeric()
                                            ->suffix('kg')
                                            ->helperText('Presione el botón abajo para leer de la balanza'),
                                        
                                        // Botón para leer tara
                                        Forms\Components\View::make('components.balanza-button')
                                            ->viewData([
                                                'targetField' => 'pesoTara',
                                                'buttonText' => '📊 Leer Tara',
                                            ]),
                                    ]),

                                // PESO NETO (CALCULADO AUTOMÁTICAMENTE)
                                Forms\Components\TextInput::make('pesoNeto')
                                    ->id('pesoNeto')
                                    ->label('Peso Neto (kg)')
                                    ->disabled()
                                    ->suffix('kg')
                                    ->dehydrated(false)
                                    ->helperText('Se calcula automáticamente: Bruto - Tara'),
                            ]),
                    ])
                    ->collapsible(),

                // Resto de los campos...
                Forms\Components\Section::make('Características del Producto')
                    ->schema([
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\TextInput::make('humedad')
                                    ->label('% de Humedad')
                                    ->numeric()
                                    ->suffix('%'),
                                
                                Forms\Components\TextInput::make('proteina')
                                    ->label('% de Proteína')
                                    ->numeric()
                                    ->suffix('%'),
                                
                                Forms\Components\TextInput::make('testWeight')
                                    ->label('Test Weight')
                                    ->numeric(),
                                
                                Forms\Components\TextInput::make('grano')
                                    ->label('% Grano Partido')
                                    ->numeric()
                                    ->suffix('%'),
                            ]),
                    ])
                    ->collapsible(),

                // JavaScript para calcular peso neto automáticamente
                Forms\Components\Hidden::make('_script')
                    ->default('')
                    ->dehydrated(false)
                    ->afterStateHydrated(function () {
                        // Este código se ejecutará cuando se cargue el formulario
                    }),
            ])
            // Script personalizado para calcular el peso neto
            ->extraAttributes([
                'x-data' => '{
                    pesoBruto: $wire.entangle("pesoBruto"),
                    pesoTara: $wire.entangle("pesoTara"),
                    calcularPesoNeto() {
                        const bruto = parseFloat(this.pesoBruto) || 0;
                        const tara = parseFloat(this.pesoTara) || 0;
                        const neto = bruto - tara;
                        const pesoNetoField = document.getElementById("pesoNeto");
                        if (pesoNetoField) {
                            pesoNetoField.value = neto.toFixed(2);
                        }
                    }
                }',
                'x-init' => '$watch("pesoBruto", () => calcularPesoNeto()); $watch("pesoTara", () => calcularPesoNeto())',
            ]);
    }

    // ... resto de los métodos de tu Resource
}
