# Configuración para Producción - balanza.barloventosrl.website

## ⚙️ Variables de Entorno (.env en servidor)

```bash
APP_NAME=BalanceControl
APP_ENV=production
APP_KEY=base64:TU_APP_KEY_AQUI  # Generar con: php artisan key:generate
APP_DEBUG=false  # IMPORTANTE: false en producción
APP_URL=https://balanza.barloventosrl.website

# Base de datos de producción
DB_CONNECTION=mysql
DB_HOST=127.0.0.1  # o la IP de tu servidor MySQL
DB_PORT=3306
DB_DATABASE=balancecontrol_prod
DB_USERNAME=tu_usuario_db
DB_PASSWORD=tu_password_db

# Configuración de la Balanza Digital EL05B
# IMPORTANTE: Ajustar según el puerto COM de tu servidor
BALANZA_PUERTO=COM3  # o el puerto que identifiques en el servidor
BALANZA_BAUD_RATE=9600
BALANZA_DATA_BITS=8
BALANZA_PARITY=none
BALANZA_STOP_BITS=1
BALANZA_FORMATO=e105
BALANZA_DECIMALES=2
BALANZA_DIVISOR=100
BALANZA_TIMEOUT=2
BALANZA_MAX_INTENTOS=10
BALANZA_DELAY_MS=500
BALANZA_HABILITADA=true
BALANZA_MODO=manual
BALANZA_INTERVALO_MS=1000
```

## 📋 Checklist de Despliegue en Producción

### 1. Configuración del Servidor
- [ ] Servidor Windows con PHP 8.x
- [ ] Extensión COM de PHP habilitada
- [ ] Balanza conectada vía USB-Serial
- [ ] Identificar puerto COM correcto

### 2. Identificar Puerto COM en el Servidor

**Desde PowerShell en el servidor:**
```powershell
Get-WMIObject Win32_SerialPort | Select-Object Name, DeviceID
```

O desde el Administrador de Dispositivos:
- Abrir Administrador de Dispositivos
- Expandir "Puertos (COM y LPT)"
- Anotar el puerto (ej: COM3, COM4, etc.)

### 3. Configuración de Apache/Nginx

**Si usas Apache, asegúrate de tener:**

En el archivo `.htaccess` (en la carpeta `public`):
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php/$1 [L]
</IfModule>
```

**Si usas Nginx:**
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### 4. Comandos de Despliegue

Ejecutar en el servidor:

```bash
# 1. Subir archivos al servidor

# 2. Instalar dependencias
composer install --optimize-autoloader --no-dev

# 3. Configurar .env
cp .env.example .env
nano .env  # Editar con los valores de producción

# 4. Generar key
php artisan key:generate

# 5. Limpiar y optimizar
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Migrar base de datos (si es necesario)
php artisan migrate --force

# 7. Configurar permisos
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache  # En Linux
# En Windows, dar permisos de escritura a IIS/Apache
```

### 5. Probar la Conexión

**URL de prueba:**
```
https://balanza.barloventosrl.website/test-balanza.html
```

**API directa:**
```
https://balanza.barloventosrl.website/api/balanza/probar-conexion
```

### 6. Seguridad en Producción

#### Agregar CORS (si es necesario)

En `config/cors.php`:
```php
'paths' => ['api/*'],
'allowed_origins' => ['https://balanza.barloventosrl.website'],
'allowed_methods' => ['GET'],
```

#### Agregar Rate Limiting

En `app/Http/Kernel.php`:
```php
'api' => [
    'throttle:60,1', // 60 requests por minuto
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
],
```

#### Proteger rutas con autenticación (opcional)

En `routes/api.php`:
```php
Route::prefix('balanza')->middleware('auth:sanctum')->group(function () {
    Route::get('/leer-peso', [BalanzaController::class, 'leerPeso']);
    Route::get('/leer-peso-estable', [BalanzaController::class, 'leerPesoEstable']);
    Route::get('/probar-conexion', [BalanzaController::class, 'probarConexion']);
});
```

### 7. Monitoreo y Logs

**Ver logs en producción:**
```bash
tail -f storage/logs/laravel.log
```

**Logs de la balanza:**
Puedes agregar logging específico modificando `BalanzaController.php`:

```php
use Illuminate\Support\Facades\Log;

public function leerPeso(Request $request): JsonResponse
{
    try {
        $resultado = $balanza->leerPeso();
        
        // Registrar lecturas exitosas
        Log::info('Lectura de balanza exitosa', [
            'peso' => $resultado['peso'],
            'estatus' => $resultado['estatus'],
            'timestamp' => now()
        ]);
        
        return response()->json([...]);
    } catch (Exception $e) {
        // Registrar errores
        Log::error('Error al leer balanza', [
            'error' => $e->getMessage(),
            'puerto' => $puerto,
            'timestamp' => now()
        ]);
        
        return response()->json([...]);
    }
}
```

### 8. Troubleshooting en Producción

#### Error: "No se pudo abrir el puerto COM"
- Verificar que el puerto COM esté disponible
- Verificar permisos del usuario que ejecuta PHP
- Reiniciar el servidor web

#### Error 404 en /api/balanza
- Verificar que mod_rewrite esté activo
- Limpiar cache: `php artisan route:cache`
- Verificar .htaccess

#### Peso no se actualiza
- Abrir consola del navegador (F12)
- Verificar errores de CORS
- Verificar que HTTPS esté configurado correctamente

## 🔒 SSL/HTTPS

Si usas HTTPS (como en tu caso), asegúrate de:

1. **Forzar HTTPS** en `AppServiceProvider.php`:
```php
use Illuminate\Support\Facades\URL;

public function boot()
{
    if ($this->app->environment('production')) {
        URL::forceScheme('https');
    }
}
```

2. **Configuración en .env:**
```bash
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
```

## 📱 URLs de Acceso

### Desarrollo Local:
```
http://localhost/balanceControl/public/test-balanza.html
http://localhost/balanceControl/public/api/balanza/probar-conexion
```

### Producción:
```
https://balanza.barloventosrl.website/test-balanza.html
https://balanza.barloventosrl.website/api/balanza/probar-conexion
```

## 🎯 Next Steps

1. [ ] Subir código al servidor de producción
2. [ ] Configurar `.env` con valores de producción
3. [ ] Identificar puerto COM en el servidor
4. [ ] Ejecutar comandos de optimización
5. [ ] Probar conexión desde https://balanza.barloventosrl.website/test-balanza.html
6. [ ] Integrar en formularios de Filament
7. [ ] Configurar SSL correctamente
8. [ ] Habilitar logging para monitoreo
