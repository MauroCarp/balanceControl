@php
    $estadoColors = [
        'reparacion' => 'bg-red-500 text-white',
        'activo' => 'bg-green-500 text-white',
        'por_llenarse' => 'bg-yellow-400 text-black',
        'lleno' => 'bg-blue-500 text-white',
    ];
@endphp

<div class="filament-widget p-6 bg-white rounded-xl shadow-sm">
    <h3 class="text-lg font-semibold mb-4">Mapa de silos</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach ($silos as $silo)
            @php
                $color = $estadoColors[$silo['estado']] ?? 'bg-gray-200';
            @endphp
            <div class="rounded-lg p-4 shadow-sm {{ $color }}">
                <div class="text-sm font-semibold">{{ $silo['nombre'] }}</div>
                <div class="text-xs">Cap: {{ $silo['capacidad'] }} tn</div>
                <div class="text-xs">Disp: {{ $silo['disponible'] }} tn</div>
                <div class="text-xs">Cultivo: {{ $silo['cultivo'] }}</div>
                <div class="text-xs">Estado: {{ str_replace('_', ' ', ucfirst($silo['estado'])) }}</div>
            </div>
        @endforeach
    </div>
</div>
