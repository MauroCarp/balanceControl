<?php

namespace App\Filament\SilosPanel\Widgets;

use App\Models\Silo;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;

class AjusteStockCeroWidget extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.silos-panel.widgets.ajuste-stock-cero-widget';

    public ?int $silo = null;

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
                ->options(Silo::orderBy('nombre')->pluck('nombre', 'id'))
                ->searchable()
                ->required(),
        ];
    }

    public function ajustar(): void
    {
        $this->form->validate();

        $silo = Silo::find($this->silo);

        if (! $silo) {
            Notification::make()
                ->title('Error')
                ->body('Silo no encontrado.')
                ->danger()
                ->send();
            return;
        }

        $silo->update(['stock_actual_kg' => 0, 'estado' => 'vacio', 'cereal' => null, 'humedad' => null,'ajuste'=>now()]);

        Notification::make()
            ->title('Stock ajustado')
            ->body("El stock de {$silo->nombre} fue ajustado a cero.")
            ->success()
            ->send();

        $this->silo = null;
        $this->form->fill();

        $this->dispatch('silo-stock-actualizado');
    }
}
