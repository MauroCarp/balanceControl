<div class="filament-widget p-6 bg-white rounded-xl shadow-sm overflow-auto">
    <h3 class="text-lg font-semibold mb-4">Detalle por silos</h3>
    <table class="min-w-full text-sm">
        <thead>
            <tr class="text-left border-b">
                <th class="py-2 pr-4">Silo</th>
                <th class="py-2 pr-4">Stock (Kg)</th>
                <th class="py-2 pr-4">Humedad (%)</th>
                <th class="py-2 pr-4">Capacidad</th>
                <th class="py-2 pr-4">Disponible</th>
                <th class="py-2 pr-4">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr class="border-b last:border-0">
                    <td class="py-2 pr-4 font-semibold">{{ $row['silo'] }}</td>
                    <td class="py-2 pr-4">{{ number_format($row['stock']) }}</td>
                    <td class="py-2 pr-4">{{ $row['humedad'] }}</td>
                    <td class="py-2 pr-4">{{ number_format($row['capacidad']) }}</td>
                    <td class="py-2 pr-4">{{ number_format($row['disponible']) }}</td>
                    <td class="py-2 pr-4">{{ $row['estado'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
