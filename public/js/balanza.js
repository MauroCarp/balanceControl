// Lector de Balanza - Frontend
class BalanzaReader {
    constructor(apiUrl = '/api/balanza/leer-peso') {
        this.apiUrl = apiUrl;
    }

    async leerPeso() {
        try {
            const response = await fetch(this.apiUrl);
            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.error || 'Error leyendo balanza');
            }
            
            return data;
        } catch (error) {
            console.error('Error:', error);
            throw error;
        }
    }

    // Método helper para Filament
    async llenarCampo(campoId) {
        try {
            const datos = await this.leerPeso();
            const campo = document.querySelector(`#${campoId}`);
            
            if (campo) {
                campo.value = datos.peso;
                campo.dispatchEvent(new Event('input', { bubbles: true }));
                return datos.peso;
            }
        } catch (error) {
            alert('Error: ' + error.message);
        }
    }
}

// Instancia global
window.balanzaReader = new BalanzaReader();
