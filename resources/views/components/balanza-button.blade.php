<div class="balanza-button-wrapper">
    <button 
        type="button" 
        class="fi-btn fi-btn-size-md fi-color-primary inline-flex items-center gap-2"
        onclick="leerPesoBalanza('{{ $targetField ?? 'pesoBruto' }}')"
        id="btn-leer-{{ $targetField ?? 'pesoBruto' }}"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
        </svg>
        {{ $buttonText ?? 'Leer Balanza' }}
    </button>
    
    <div 
        id="status-{{ $targetField ?? 'pesoBruto' }}" 
        class="mt-2 text-sm"
        style="display: none;"
    ></div>
</div>

<script>
    function leerPesoBalanza(targetField) {
        const button = document.getElementById(`btn-leer-${targetField}`);
        const status = document.getElementById(`status-${targetField}`);
        
        // Deshabilitar botón
        button.disabled = true;
        button.innerHTML = '<svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Leyendo...';
        
        // Mostrar status
        status.style.display = 'block';
        status.className = 'mt-2 text-sm text-yellow-600';
        status.innerHTML = '⏳ Esperando peso estable...';
        
        // Realizar la petición
        fetch('/api/balanza/leer-peso-estable')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Actualizar el campo
                    const field = document.getElementById(targetField);
                    if (field) {
                        field.value = data.data.peso_formateado;
                        field.dispatchEvent(new Event('input', { bubbles: true }));
                        field.dispatchEvent(new Event('change', { bubbles: true }));
                        
                        // Si es Alpine.js
                        if (field._x_model) {
                            field._x_model.set(data.data.peso_formateado);
                        }
                    }
                    
                    // Mostrar éxito
                    status.className = 'mt-2 text-sm text-green-600';
                    status.innerHTML = `✅ Peso: ${data.data.peso_formateado} kg ${data.data.estatus.tipo_peso === 'neto' ? '(Neto)' : '(Bruto)'}`;
                    
                    // Ocultar mensaje después de 5 segundos
                    setTimeout(() => {
                        status.style.display = 'none';
                    }, 5000);
                } else {
                    throw new Error(data.message || 'Error al leer el peso');
                }
            })
            .catch(error => {
                // Mostrar error
                status.className = 'mt-2 text-sm text-red-600';
                status.innerHTML = `❌ Error: ${error.message}`;
            })
            .finally(() => {
                // Rehabilitar botón
                button.disabled = false;
                button.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg> {{ $buttonText ?? "Leer Balanza" }}';
            });
    }
</script>
