<x-filament::widget>
    <x-slot name="header">
        <h2 class="text-xl font-bold">Acciones rápidas</h2>
    </x-slot>

    <div class="p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-filament::button tag="a" href="{{ route('filament.admin.pages.pagina-uno') }}" class="w-full">
                Ir a Página Uno
            </x-filament::button>

            <x-filament::button tag="a" href="{{ route('filament.admin.pages.pagina-dos') }}" class="w-full">
                Ir a Página Dos
            </x-filament::button>
        </div>
    </div>
</x-filament::widget>