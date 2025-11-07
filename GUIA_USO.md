# 🎯 Guía de Uso - Balanza Digital con Laravel

## 📋 Descripción

Sistema para leer datos de balanza digital RS232 desde Laravel usando un gateway HTTP local.

```
Balanza RS232 → Tu PC (Gateway PHP) → Internet (ngrok) → Servidor Linux (Laravel)
```

---

## 🚀 Instalación Paso a Paso

### PASO 1: Configurar Gateway en tu PC

1. **Conectar la balanza** a tu PC mediante USB-Serial
2. **Identificar el puerto COM**:
   - Ir a "Administrador de dispositivos" → "Puertos (COM y LPT)"
   - Anotar el número (ej: COM3)

3. **Iniciar el gateway**:
   ```powershell
   cd c:\wamp64\www\balanceControl
   php -S 0.0.0.0:8080 gateway-balanza.php
   ```

4. **Probar localmente**:
   ```
   http://localhost:8080/gateway-balanza.php?puerto=COM3
   ```
   
   Deberías ver algo como:
   ```json
   {
     "success": true,
     "peso": 1234.56,
     "unidad": "kg",
     "estable": true
   }
   ```

### PASO 2: Exponer a Internet con ngrok

1. **Descargar ngrok**: https://ngrok.com/download

2. **Ejecutar ngrok**:
   ```powershell
   ngrok http 8080
   ```

3. **Copiar la URL pública** (ej: `https://abc123.ngrok.io`)

### PASO 3: Configurar Laravel en Producción

1. **Editar archivo `.env` en el servidor**:
   ```env
   BALANZA_GATEWAY_URL=https://abc123.ngrok.io/gateway-balanza.php
   BALANZA_PUERTO=COM3
   ```

2. **Limpiar caché**:
   ```bash
   php artisan config:cache
   ```

### PASO 4: Usar en tus Formularios

#### Opción A: JavaScript directo

```html
<button onclick="leerBalanza()">Leer Peso</button>
<input type="text" id="peso_bruto" />

<script>
async function leerBalanza() {
    const response = await fetch('/api/balanza/leer-peso');
    const data = await response.json();
    
    if (data.success) {
        document.getElementById('peso_bruto').value = data.peso;
    } else {
        alert('Error: ' + data.error);
    }
}
</script>
```

#### Opción B: Con el helper incluido

```html
<script src="/js/balanza.js"></script>

<button onclick="balanzaReader.llenarCampo('peso_bruto')">
    Leer Peso
</button>
<input type="text" id="peso_bruto" />
```

#### Opción C: En Filament Form

```php
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Actions\Action;

TextInput::make('peso_bruto')
    ->label('Peso Bruto')
    ->suffix('kg')
    ->suffixAction(
        Action::make('leerPeso')
            ->label('Leer Balanza')
            ->icon('heroicon-o-scale')
            ->action(function (Set $set) {
                // JavaScript que lee la balanza
            })
            ->extraAttributes([
                'onclick' => 'balanzaReader.llenarCampo("data.peso_bruto")'
            ])
    )
```

---

## 🧪 Testing

### 1. Probar Gateway Local
```powershell
# Terminal 1: Iniciar gateway
php -S 0.0.0.0:8080 gateway-balanza.php

# Terminal 2: Probar
curl http://localhost:8080/gateway-balanza.php?puerto=COM3
```

### 2. Probar con ngrok
```powershell
# Terminal 1: Gateway
php -S 0.0.0.0:8080 gateway-balanza.php

# Terminal 2: ngrok
ngrok http 8080

# Terminal 3: Probar
curl https://abc123.ngrok.io/gateway-balanza.php?puerto=COM3
```

### 3. Probar Laravel API
```bash
curl https://tudominio.com/api/balanza/leer-peso
```

---

## ⚙️ Configuración

### Variables de Entorno (.env)

```env
# URL del gateway (cambiar abc123 por tu URL de ngrok)
BALANZA_GATEWAY_URL=https://abc123.ngrok.io/gateway-balanza.php

# Puerto COM en tu PC
BALANZA_PUERTO=COM3
```

---

## 🔧 Troubleshooting

### Gateway no responde
```
Error: Gateway no responde
```

**Soluciones**:
- Verificar que el gateway esté corriendo: `php -S 0.0.0.0:8080 gateway-balanza.php`
- Verificar que ngrok esté activo
- Verificar la URL en `.env`

### No se pudo abrir puerto COM
```
{
  "success": false,
  "error": "No se pudo abrir COM3"
}
```

**Soluciones**:
- Verificar que la balanza esté encendida
- Verificar el puerto correcto en Administrador de Dispositivos
- Cerrar otros programas que usen el puerto
- Cambiar el parámetro: `?puerto=COM1` o `?puerto=COM2`

### URL de ngrok cambia
Cada vez que reinicias ngrok, la URL cambia. Soluciones:

**Opción 1: ngrok Pro** (USD $8/mes) - URL fija
```powershell
ngrok http 8080 --domain=tu-dominio-fijo.ngrok.io
```

**Opción 2: Script de actualización automática**
Crear `actualizar-env.ps1`:
```powershell
$url = ngrok api tunnels list | ConvertFrom-Json | Select -ExpandProperty tunnels | Select -First 1 -ExpandProperty public_url
ssh usuario@servidor "sed -i 's|BALANZA_GATEWAY_URL=.*|BALANZA_GATEWAY_URL=$url/gateway-balanza.php|' /ruta/.env && php artisan config:cache"
```

---

## 💰 Costos

| Servicio | Costo | Propósito |
|----------|-------|-----------|
| ngrok Free | Gratis | URL pública temporal |
| ngrok Pro | USD $8/mes | URL pública fija |
| Cloudflare Tunnel | Gratis | Alternativa a ngrok |

---

## 🔒 Seguridad (Opcional)

Para agregar autenticación, editar `gateway-balanza.php`:

```php
// Cambiar al inicio del archivo
define('API_KEY', 'tu-clave-secreta-123');

// Verificar
if ($_GET['key'] !== API_KEY) {
    die(json_encode(['success' => false, 'error' => 'No autorizado']));
}
```

Luego en `.env`:
```env
BALANZA_GATEWAY_URL=https://abc123.ngrok.io/gateway-balanza.php?key=tu-clave-secreta-123
```

---

## 📚 Endpoints API

### GET /api/balanza/leer-peso

**Response exitoso**:
```json
{
  "success": true,
  "peso": 1234.56,
  "unidad": "kg",
  "estable": true
}
```

**Response con error**:
```json
{
  "success": false,
  "error": "Gateway no responde"
}
```

---

## ✅ Checklist de Implementación

- [ ] Balanza conectada a PC vía USB-Serial
- [ ] Puerto COM identificado
- [ ] Gateway ejecutándose: `php -S 0.0.0.0:8080 gateway-balanza.php`
- [ ] ngrok ejecutándose: `ngrok http 8080`
- [ ] URL de ngrok copiada
- [ ] `.env` actualizado en servidor con BALANZA_GATEWAY_URL
- [ ] Caché limpiada: `php artisan config:cache`
- [ ] Probada la API: `/api/balanza/leer-peso`
- [ ] Integrado en formularios

---

## 🎓 Ejemplo Completo

Ver archivo `resources/views/ejemplos/balanza-demo.blade.php` para un ejemplo completo de integración.

---

## 📞 Soporte

Si tienes problemas, verifica:
1. Gateway corriendo en tu PC
2. ngrok activo
3. URL correcta en `.env`
4. Balanza encendida
5. Puerto COM correcto
