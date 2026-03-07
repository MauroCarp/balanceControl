<x-filament-widgets::widget class="fi-wi-detalle-silos">
    <x-filament::section heading="Detalle por Silos">
        @php
            $estadoStyles = [
                'Activo'        => 'background-color:#22c55e;color:#fff;',
                'Vacío'         => 'background-color:#e5e7eb;color:#374151;',
                'Lleno'         => 'background-color:#3b82f6;color:#fff;',
                'En reparación' => 'background-color:#ef4444;color:#fff;',
            ];
        @endphp
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-white/10 text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                        <th class="py-3 pr-4">Silo</th>
                        <th class="py-3 pr-4">Cereal</th>
                        <th class="py-3 pr-4">Stock (Kg)</th>
                        <th class="py-3 pr-4">Humedad (%)</th>
                        <th class="py-3 pr-4">Capacidad</th>
                        <th class="py-3 pr-4">Disponible</th>
                        <th class="py-3 pr-4">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                    @foreach ($rows as $row)
                        <tr class="text-gray-700 dark:text-gray-300">
                            <td class="py-3 pr-4 font-semibold">{{ $row['silo'] }}</td>
                            <td class="py-3 pr-4">{{ $row['cereal'] }}</td>
                            <td class="py-3 pr-4">{{ number_format($row['stock']) }}</td>
                            <td class="py-3 pr-4">{{ $row['humedad'] }}</td>
                            <td class="py-3 pr-4">{{ number_format($row['capacidad']) }}</td>
                            <td class="py-3 pr-4">{{ number_format($row['disponible']) }}</td>
                            <td class="py-3 pr-4">
                                <span style="{{ $estadoStyles[$row['estado']] ?? 'background-color:#e5e7eb;color:#374151;' }}; padding:2px 10px; border-radius:9999px; font-size:0.75rem; font-weight:600; white-space:nowrap;">
                                    {{ $row['estado'] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
