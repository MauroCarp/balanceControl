<div class="filament-widget p-6 bg-white rounded-xl shadow-sm">
    <h3 class="text-lg font-semibold mb-4">Ajuste de Stock a cero</h3>
    <form wire:submit.prevent="ajustar" class="space-y-4">
        {{ $this->form }}
        <div>
            <x-filament::button type="submit" color="danger">
                Ajustar a cero
            </x-filament::button>
        </div>
    </form>
</div>
