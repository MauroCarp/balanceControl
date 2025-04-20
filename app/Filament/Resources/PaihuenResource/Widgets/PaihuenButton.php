<?php

namespace App\Filament\Resources\PaihuenResource\Widgets;

use Filament\Widgets\Widget;

class PaihuenButton extends Widget
{
    protected static string $view = 'filament.resources.paihuen-resource.widgets.paihuen-button';

    protected static ?int $sort = 2 ;

    protected function getColumns(): int {
        return 1;
    }

    public function getColumnSpan(): int|string{
        return 'full';
    }
}
