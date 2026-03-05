<?php

namespace App\Filament\SilosPanel\Widgets;

use Filament\Forms; 
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;

class AjusteStockCeroWidget extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.silos-panel.widgets.ajuste-stock-cero-widget';

    public ?string $silo = null;

    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 3;
    protected static bool $isLazy = false;

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('silo')
                ->label('Silo')
                ->placeholder('Selecciona un silo')
                ->options([
                    'silo_1' => 'Silo 1',
                    'silo_2' => 'Silo 2',
                    'silo_3' => 'Silo 3',
                ])
                ->searchable()
                ->required(),
        ];
    }

    public function ajustar(): void
    {
        Notification::make()
            ->title('Ajuste de stock')
            ->body('Implementar logica para ajustar el silo seleccionado a cero.')
            ->success()
            ->send();
    }
}
