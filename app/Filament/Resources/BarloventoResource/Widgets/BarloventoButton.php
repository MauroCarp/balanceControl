<?php

namespace App\Filament\Resources\BarloventoResource\Widgets;

use Filament\Widgets\Widget;

class BarloventoButton extends Widget
{
    protected static string $view = 'filament.resources.barlovento-resource.widgets.barlovento-button';

    protected static ?int $sort = 1;

    protected function getColumns(): int {
        return 1;
    }

    public function getColumnSpan(): int|string{
        return 'full';
    }
}
