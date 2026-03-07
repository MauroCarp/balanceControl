@php
    $estadoStyles = [
        'activo'        => 'background-color:#22c55e;color:#fff;',
        'vacio'         => 'background-color:#e5e7eb;color:#374151;',
        'lleno'         => 'background-color:#3b82f6;color:#fff;',
        'en_reparacion' => 'background-color:#ef4444;color:#fff;',
    ];
    $estadoLabels = [
        'activo'        => 'Activo',
        'vacio'         => 'Vacío',
        'lleno'         => 'Lleno',
        'en_reparacion' => 'En reparación',
    ];
@endphp

<x-filament-widgets::widget class="col-span-full">
    <x-filament::section>
        <x-slot name="heading">Mapa de Silos</x-slot>
        <div style="display:grid;grid-template-columns:repeat(5,minmax(0,180px));justify-content:center;gap:1.25rem;">
            @foreach ($silos as $silo)
                <div class="rounded-lg p-4 shadow-sm" style="{{ $estadoStyles[$silo['estado']] ?? 'background-color:#e5e7eb;color:#374151;' }}">
                    <div class="text-sm font-semibold">{{ $silo['nombre'] }}</div>
                    <div class="text-xs">Cap: {{ number_format($silo['capacidad'], 0, ',', '.') }} kg</div>
                    <div class="text-xs">Disp: {{ number_format($silo['disponible'], 0, ',', '.') }} kg</div>
                    <div class="text-xs">Cultivo: {{ $silo['cultivo'] }}</div>
                    <div class="text-xs">Estado: {{ $estadoLabels[$silo['estado']] ?? ucfirst($silo['estado']) }}</div>
                </div>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>