<x-filament::widget>
    <x-slot name="header">
        <h2 class="text-xl font-bold">Acciones rápidas</h2>
    </x-slot>

    <div class="p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-filament::button tag="a" 
            href="#" 
            id="btnBarlovento"
            class="w-full" style="font-size:5em;padding: 1em 2em;">
            BARLOVENTO
            </x-filament::button>

            <x-filament::button tag="a" 
            href="/admin/paihuen-cereales/create" 
            id="btnPaihuen"
            class="w-full" style="font-size:5em;padding: 1em 2em;">
            PAIHUEN
            </x-filament::button>
        </div>
    </div>
    <div class="p-4 hidden" id="btnsBarlovento">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-filament::button tag="a" 
            href="/admin/barlovento-ingresos/create" 
            id="btnIngresoHacienda"
            class="w-full" style="font-size:2em;padding: 1em 1em;">
            Ingreso Hacienda
            </x-filament::button>
            
            <x-filament::button tag="a" 
            href="/admin/barlovento-egresos/create" 
            id="btnEgresoHacienda"
            class="w-full" style="font-size:2em;padding: 1em 1em;">
            Egreso Hacienda
            </x-filament::button>

            <x-filament::button tag="a" 
            href="/admin/barlovento-cereales/create" 
            id="btnIngresoCereal"
            class="w-full" style="font-size:2em;padding: 1em 1em;line-height: 1.2em;">
            Ingreso Cereal / Otro Ingreso
            </x-filament::button>
        </div>
    </div>
 

</x-filament::widget>