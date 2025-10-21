/**
 * Servicio JavaScript para leer datos de la balanza digital
 * Integración con formularios Filament
 */

class BalanzaReader {
    constructor(options = {}) {
        this.apiUrl = options.apiUrl || '/api/balanza';
        this.autoPoll = options.autoPoll || false;
        this.pollInterval = options.pollInterval || 1000;
        this.onPesoRead = options.onPesoRead || null;
        this.onError = options.onError || null;
        this.pollTimer = null;
        this.isReading = false;
    }

    /**
     * Lee el peso actual de la balanza
     */
    async leerPeso(puerto = null, baudRate = null) {
        if (this.isReading) {
            console.warn('Ya hay una lectura en progreso');
            return null;
        }

        this.isReading = true;

        try {
            const params = new URLSearchParams();
            if (puerto) params.append('puerto', puerto);
            if (baudRate) params.append('baud_rate', baudRate);

            const response = await fetch(`${this.apiUrl}/leer-peso?${params.toString()}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                if (this.onPesoRead) {
                    this.onPesoRead(data.data);
                }
                return data.data;
            } else {
                throw new Error(data.message || 'Error al leer el peso');
            }
        } catch (error) {
            console.error('Error al leer la balanza:', error);
            if (this.onError) {
                this.onError(error);
            }
            return null;
        } finally {
            this.isReading = false;
        }
    }

    /**
     * Lee el peso y espera a que esté estable
     */
    async leerPesoEstable(puerto = null, baudRate = null, maxIntentos = 10) {
        if (this.isReading) {
            console.warn('Ya hay una lectura en progreso');
            return null;
        }

        this.isReading = true;

        try {
            const params = new URLSearchParams();
            if (puerto) params.append('puerto', puerto);
            if (baudRate) params.append('baud_rate', baudRate);
            if (maxIntentos) params.append('max_intentos', maxIntentos);

            const response = await fetch(`${this.apiUrl}/leer-peso-estable?${params.toString()}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                if (this.onPesoRead) {
                    this.onPesoRead(data.data);
                }
                return data.data;
            } else {
                throw new Error(data.message || 'Error al leer el peso estable');
            }
        } catch (error) {
            console.error('Error al leer peso estable:', error);
            if (this.onError) {
                this.onError(error);
            }
            return null;
        } finally {
            this.isReading = false;
        }
    }

    /**
     * Prueba la conexión con la balanza
     */
    async probarConexion(puerto = null, baudRate = null) {
        try {
            const params = new URLSearchParams();
            if (puerto) params.append('puerto', puerto);
            if (baudRate) params.append('baud_rate', baudRate);

            const response = await fetch(`${this.apiUrl}/probar-conexion?${params.toString()}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                console.log('Conexión exitosa con la balanza:', data);
                return true;
            } else {
                console.error('Error de conexión:', data.message);
                return false;
            }
        } catch (error) {
            console.error('Error al probar conexión:', error);
            return false;
        }
    }

    /**
     * Inicia la lectura automática periódica
     */
    startAutoRead() {
        if (this.pollTimer) {
            console.warn('La lectura automática ya está activa');
            return;
        }

        this.pollTimer = setInterval(() => {
            this.leerPeso();
        }, this.pollInterval);

        console.log('Lectura automática iniciada');
    }

    /**
     * Detiene la lectura automática
     */
    stopAutoRead() {
        if (this.pollTimer) {
            clearInterval(this.pollTimer);
            this.pollTimer = null;
            console.log('Lectura automática detenida');
        }
    }

    /**
     * Actualiza un campo de Filament con el peso leído
     */
    static actualizarCampoFilament(fieldId, peso) {
        const field = document.querySelector(`#${fieldId}`);
        if (field) {
            // Activar el input de Filament
            field.value = peso;
            
            // Disparar eventos para que Filament detecte el cambio
            field.dispatchEvent(new Event('input', { bubbles: true }));
            field.dispatchEvent(new Event('change', { bubbles: true }));
            
            // Si es Alpine.js (usado por Filament)
            if (field._x_model) {
                field._x_model.set(peso);
            }
        } else {
            console.warn(`Campo ${fieldId} no encontrado`);
        }
    }

    /**
     * Agrega un botón de lectura a un formulario de Filament
     */
    static agregarBotonLectura(targetFieldId, options = {}) {
        const config = {
            buttonText: options.buttonText || '📊 Leer Balanza',
            buttonClass: options.buttonClass || 'fi-btn fi-btn-size-md fi-color-primary',
            waitForStable: options.waitForStable || true,
            showStatus: options.showStatus !== false,
            ...options
        };

        const field = document.querySelector(`#${targetFieldId}`);
        if (!field) {
            console.error(`Campo ${targetFieldId} no encontrado`);
            return;
        }

        const fieldWrapper = field.closest('.fi-fo-text-input');
        if (!fieldWrapper) {
            console.error('No se encontró el wrapper del campo');
            return;
        }

        // Crear el botón
        const button = document.createElement('button');
        button.type = 'button';
        button.className = config.buttonClass;
        button.style.marginTop = '8px';
        button.innerHTML = config.buttonText;

        // Crear indicador de estado
        let statusDiv = null;
        if (config.showStatus) {
            statusDiv = document.createElement('div');
            statusDiv.style.marginTop = '4px';
            statusDiv.style.fontSize = '12px';
            statusDiv.style.color = '#666';
        }

        // Crear instancia del reader
        const reader = new BalanzaReader({
            onPesoRead: (data) => {
                BalanzaReader.actualizarCampoFilament(targetFieldId, data.peso_formateado);
                if (statusDiv) {
                    statusDiv.innerHTML = `✅ Peso: ${data.peso_formateado} kg ${data.estatus.tipo_peso === 'neto' ? '(Neto)' : '(Bruto)'}`;
                    statusDiv.style.color = '#22c55e';
                }
            },
            onError: (error) => {
                if (statusDiv) {
                    statusDiv.innerHTML = `❌ Error: ${error.message}`;
                    statusDiv.style.color = '#ef4444';
                }
            }
        });

        // Evento del botón
        button.addEventListener('click', async () => {
            button.disabled = true;
            button.innerHTML = '⏳ Leyendo...';
            
            if (statusDiv) {
                statusDiv.innerHTML = '⏳ Esperando peso estable...';
                statusDiv.style.color = '#f59e0b';
            }

            if (config.waitForStable) {
                await reader.leerPesoEstable();
            } else {
                await reader.leerPeso();
            }

            button.disabled = false;
            button.innerHTML = config.buttonText;
        });

        // Agregar elementos al DOM
        fieldWrapper.appendChild(button);
        if (statusDiv) {
            fieldWrapper.appendChild(statusDiv);
        }

        return reader;
    }
}

// Hacer disponible globalmente
window.BalanzaReader = BalanzaReader;

// Auto-inicialización para formularios con data-balanza
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-balanza-field]').forEach(element => {
        const fieldId = element.dataset.balanzaField;
        const options = {
            buttonText: element.dataset.balanzaButton || '📊 Leer Balanza',
            waitForStable: element.dataset.balanzaStable !== 'false'
        };
        
        BalanzaReader.agregarBotonLectura(fieldId, options);
    });
});
