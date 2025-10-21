# Integración de Balanza Digital EL05B con Sistema de Control de Balanza

## 📋 Descripción General

Este documento describe cómo integrar la balanza digital **EL05B** que se comunica por **RS232C** con tu sistema Laravel/Filament para la carga automática de datos cuando un camión ingresa a la balanza.

## 🔧 Configuración de la Balanza

### Especificaciones Técnicas
- **Modelo**: Indicador de Peso EL05B
- **Comunicación**: RS232C (Puerto Serie)
- **Formato de datos**: e105 (`<estatus><peso><CR>`)
- **Velocidades soportadas**: 1200, 2400, 4800, 9600 baud
- **Configuración recomendada**: 9600 baud, 8 bits, sin paridad

### Configurar la Balanza
1. Accede al menú de configuración de tu balanza
2. **P.6**: Establece la velocidad a **9600 baud**
3. **P.7**: Configura **8n** (8 bits sin paridad)
4. **P.8**: Selecciona el formato **"e105"**

## 🚀 Instalación

### Paso 1: Configurar Variables de Entorno

Agrega estas líneas a tu archivo `.env`:

```bash
# Configuración de la Balanza Digital EL05B
BALANZA_PUERTO=COM1              # Puerto donde está conectada (COM1, COM2, etc.)
BALANZA_BAUD_RATE=9600           # Velocidad de comunicación
BALANZA_DATA_BITS=8              # Bits de datos
BALANZA_PARITY=none              # Paridad (none, even, odd)
BALANZA_STOP_BITS=1              # Bits de parada
BALANZA_DECIMALES=2              # Número de decimales en el peso
BALANZA_DIVISOR=100              # Divisor para convertir el peso (ej: 123456 -> 1234.56)
BALANZA_HABILITADA=true          # Habilitar/deshabilitar la balanza
```

**Nota para Windows**: Si la balanza está conectada al puerto COM1, usa `COM1`. Para COM2, usa `COM2`, etc.

### Paso 2: Limpiar la Caché de Configuración

```bash
php artisan config:clear
php artisan config:cache
```

### Paso 3: Verificar Permisos del Puerto (Solo Windows)

En Windows, asegúrate de que el usuario que ejecuta PHP tenga permisos para acceder al puerto COM. Esto generalmente no es un problema, pero si encuentras errores de permisos:

1. Abre el "Administrador de dispositivos"
2. Busca "Puertos (COM y LPT)"
3. Haz clic derecho en tu puerto COM → Propiedades
4. Verifica que el puerto esté funcionando correctamente

## 📱 Uso en Formularios de Filament

### Opción 1: Agregar Botón Automáticamente con Data Attributes

En tu Resource de Filament (por ejemplo, `BarloventoCerealesResource.php`), modifica el campo de peso:

```php
Forms\Components\TextInput::make('pesoBruto')
    ->id('pesoBruto')
    ->label('Peso Bruto')
    ->required()
    ->numeric()
    ->extraAttributes([
        'data-balanza-field' => 'pesoBruto',
        'data-balanza-button' => '📊 Leer Peso Bruto',
        'data-balanza-stable' => 'true'
    ]),
```

### Opción 2: Agregar Botón Manualmente con JavaScript

Agrega este código al final de tu formulario:

```php
Forms\Components\Section::make('Lectura de Balanza')
    ->schema([
        Forms\Components\TextInput::make('pesoBruto')
            ->id('pesoBruto')
            ->label('Peso Bruto')
            ->required()
            ->numeric(),
        
        Forms\Components\View::make('components.balanza-button')
            ->viewData([
                'targetField' => 'pesoBruto',
                'buttonText' => 'Leer Peso Bruto',
            ]),
    ])
```

### Opción 3: Script Manual en el Layout

Agrega el script en el head del layout de Filament:

1. Publica las vistas de Filament (si aún no lo has hecho):
```bash
php artisan vendor:publish --tag=filament-views
```

2. Edita `resources/views/vendor/filament/components/layouts/app.blade.php`

3. Agrega antes del cierre de `</head>`:
```html
<script src="{{ asset('js/balanza-reader.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Para Peso Bruto
        BalanzaReader.agregarBotonLectura('pesoBruto', {
            buttonText: '📊 Leer Peso Bruto',
            waitForStable: true
        });
        
        // Para Tara
        BalanzaReader.agregarBotonLectura('pesoTara', {
            buttonText: '📊 Leer Tara',
            waitForStable: true
        });
    });
</script>
```

## 🧪 Pruebas

### Probar la Conexión con la Balanza

Puedes probar la conexión desde el navegador o con cURL:

#### Desde el navegador:
```
http://localhost:8000/balanceControl/public/api/balanza/probar-conexion
```

#### Con cURL (PowerShell):
```powershell
Invoke-WebRequest -Uri "http://localhost:8000/balanceControl/public/api/balanza/probar-conexion" -Method GET
```

#### Respuesta esperada exitosa:
```json
{
  "success": true,
  "data": {
    "peso": 1234.56,
    "estatus": {
      "tipo_peso": "bruto",
      "centro_cero": false,
      "equilibrio": true,
      "negativo": false,
      "fuera_rango": false
    },
    "peso_formateado": "1234.56",
    "en_equilibrio": true,
    "valido": true
  },
  "message": "Conexión exitosa con la balanza",
  "config": {
    "puerto": "COM1",
    "baud_rate": 9600
  }
}
```

### Leer Peso Manualmente
```
http://localhost:8000/balanceControl/public/api/balanza/leer-peso
```

### Leer Peso Estable (espera hasta que el peso se estabilice)
```
http://localhost:8000/balanceControl/public/api/balanza/leer-peso-estable
```

## 🔍 Solución de Problemas

### Error: "No se pudo abrir el puerto COM1"

**Causas posibles**:
1. La balanza no está conectada
2. El puerto está siendo usado por otra aplicación
3. El puerto COM especificado es incorrecto

**Solución**:
- Verifica que la balanza esté encendida y conectada
- Cierra otras aplicaciones que puedan estar usando el puerto
- Verifica el puerto correcto en el Administrador de dispositivos de Windows
- Actualiza la variable `BALANZA_PUERTO` en el archivo `.env`

### Error: "No se recibieron datos de la balanza"

**Causas posibles**:
1. La velocidad de baudios no coincide
2. La configuración de bits/paridad es incorrecta
3. La balanza no está configurada para transmitir automáticamente

**Solución**:
- Verifica la configuración P.6, P.7 y P.8 de la balanza
- Asegúrate de que la balanza esté en modo de transmisión continua o manual
- Intenta con diferentes velocidades (1200, 2400, 4800, 9600)

### El peso no se actualiza en el formulario

**Solución**:
1. Verifica que el campo tenga un `id` único
2. Abre la consola del navegador (F12) y busca errores
3. Asegúrate de que el script `balanza-reader.js` esté cargado
4. Verifica que el campo sea de tipo `TextInput` y tenga `numeric()`

### Peso incorrecto o con decimales mal posicionados

**Solución**:
Ajusta el divisor en tu archivo `.env`. Si la balanza envía `123456` y el peso real es:
- **12345.6 kg**: `BALANZA_DIVISOR=10`
- **1234.56 kg**: `BALANZA_DIVISOR=100`
- **123.456 kg**: `BALANZA_DIVISOR=1000`

## 📊 API Endpoints

### GET /api/balanza/leer-peso
Lee el peso actual de la balanza.

**Parámetros opcionales**:
- `puerto`: Puerto COM (por defecto: configuración .env)
- `baud_rate`: Velocidad (por defecto: configuración .env)

**Respuesta**:
```json
{
  "success": true,
  "data": {
    "peso": 1234.56,
    "estatus": {...},
    "peso_formateado": "1234.56",
    "en_equilibrio": true,
    "valido": true
  }
}
```

### GET /api/balanza/leer-peso-estable
Lee el peso y espera hasta que esté estable.

**Parámetros opcionales**:
- `puerto`: Puerto COM
- `baud_rate`: Velocidad
- `max_intentos`: Máximo número de intentos (por defecto: 10)

### GET /api/balanza/probar-conexion
Prueba la conexión con la balanza.

## 🔐 Seguridad

Para producción, considera agregar autenticación a las rutas de la API:

```php
Route::prefix('balanza')->middleware('auth:sanctum')->group(function () {
    Route::get('/leer-peso', [BalanzaController::class, 'leerPeso']);
    Route::get('/leer-peso-estable', [BalanzaController::class, 'leerPesoEstable']);
    Route::get('/probar-conexion', [BalanzaController::class, 'probarConexion']);
});
```

## 📈 Características Avanzadas

### Lectura Automática Periódica

Para habilitar la lectura automática cada segundo:

```javascript
const reader = new BalanzaReader({
    autoPoll: true,
    pollInterval: 1000,
    onPesoRead: (data) => {
        console.log('Peso leído:', data.peso);
        BalanzaReader.actualizarCampoFilament('pesoBruto', data.peso_formateado);
    }
});

reader.startAutoRead();
// Para detener: reader.stopAutoRead();
```

### Validación de Peso Estable

El sistema verifica automáticamente que:
- ✅ El peso esté en equilibrio (no en movimiento)
- ✅ El peso no esté fuera de rango
- ✅ El peso sea válido

## 🛠️ Mantenimiento

### Actualizar Configuración
Después de cambiar el archivo `.env`:
```bash
php artisan config:clear
```

### Logs
Los errores se registran en `storage/logs/laravel.log`

### Backup de Configuración
Guarda una copia de tu archivo `.env` y `config/balanza.php`

## 📞 Soporte

Si encuentras problemas:
1. Verifica los logs en `storage/logs/laravel.log`
2. Revisa la configuración de la balanza (P.6, P.7, P.8)
3. Prueba la conexión con el endpoint `/api/balanza/probar-conexion`
4. Verifica el puerto COM en el Administrador de dispositivos

## 📝 Notas Adicionales

- **Windows**: Los puertos COM generalmente son `COM1`, `COM2`, etc.
- **Linux**: Los puertos serie son `/dev/ttyS0`, `/dev/ttyUSB0`, etc.
- La balanza debe estar configurada en formato **e105** para que funcione correctamente
- El sistema espera que el peso venga con el formato: 1 byte de estatus + 6 caracteres de peso + CR

## ✅ Checklist de Instalación

- [ ] Balanza conectada físicamente al puerto COM
- [ ] Balanza configurada (P.6: 9600, P.7: 8n, P.8: e105)
- [ ] Variables `.env` configuradas
- [ ] Cache de configuración limpiada
- [ ] Script JavaScript agregado al layout
- [ ] Campos de formulario con ID únicos
- [ ] Conexión probada con `/api/balanza/probar-conexion`
- [ ] Botones agregados a los formularios
- [ ] Pruebas realizadas con peso real

¡Listo! Tu sistema ahora puede leer automáticamente los datos de la balanza digital.
