<div class="filament-widget p-6 bg-white rounded-xl shadow-sm">
    <h3 class="text-lg font-semibold mb-4">Ajuste de Stock a cero</h3>
    <form wire:submit.prevent="ajustar" class="space-y-4">
        {{ $this->form }}
        <div class="flex items-center gap-3">
            <x-filament::button type="submit" color="danger" wire:loading.attr="disabled" wire:target="ajustar">
                <span wire:loading.remove wire:target="ajustar">Ajustar a cero</span>
                <span wire:loading wire:target="ajustar" class="flex items-center gap-2">
                    Procesando...
                </span>
            </x-filament::button>
        </div>
    </form>
</div>
