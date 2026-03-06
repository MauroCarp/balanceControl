<x-filament-widgets::widget class="fi-wi-detalle-silos">
    <x-filament::section heading="Detalle por Silos">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-white/10 text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                        <th class="py-3 pr-4">Silo</th>
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
                            <td class="py-3 pr-4">{{ number_format($row['stock']) }}</td>
                            <td class="py-3 pr-4">{{ $row['humedad'] }}</td>
                            <td class="py-3 pr-4">{{ number_format($row['capacidad']) }}</td>
                            <td class="py-3 pr-4">{{ number_format($row['disponible']) }}</td>
                            <td class="py-3 pr-4">{{ $row['estado'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
