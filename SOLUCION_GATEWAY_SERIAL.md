# 🌐 Solución: Gateway Serial a HTTP

## Concepto

```
Balanza (RS232) → Adaptador USB → PC/Raspberry → Script Gateway → Internet → Servidor Ferozo
```

En lugar de conectar la balanza directamente al servidor de Ferozo, la conectas a una computadora local (puede ser tu PC Windows 7, una Raspberry Pi, o cualquier PC) que actúe como "puente" entre la balanza y el servidor.

---

## 🎯 Arquitectura

```
┌──────────────┐     RS232     ┌─────────────┐     HTTP      ┌──────────────┐
│   Balanza    │ ────────────> │ PC Local    │ ───────────> │ Servidor Web │
│   EL05B      │   USB-Serial  │ (Gateway)   │  Internet    │   Ferozo     │
└──────────────┘               └─────────────┘              └──────────────┘
                                     │
                                     ├─ Lee puerto COM1
                                     ├─ Expone API HTTP local
                                     └─ Tu app consulta esta API
```

---

## 💻 Implementación: Gateway en PHP (para tu PC Windows)

### Paso 1: Crear script gateway local

Crear archivo: `C:\gateway-balanza\servidor-balanza.php`

```php
<?php
/**
 * Gateway Serial-to-HTTP para Balanza
 * Este script se ejecuta en la PC local donde está conectada la balanza
 * Expone una API HTTP que puede ser consultada desde Internet
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Permitir CORS

$puerto = $_GET['puerto'] ?? 'COM1';
$accion = $_GET['accion'] ?? 'leer';

function leerBalanza($puerto) {
    try {
        $handle = @fopen($puerto . ':', "r+b");
        
        if ($handle === false) {
            throw new Exception("No se pudo abrir puerto {$puerto}");
        }
        
        stream_set_timeout($handle, 2);
        
        // Limpiar buffer
        stream_set_blocking($handle, false);
        while (fread($handle, 1024)) {}
        stream_set_blocking($handle, true);
        
        // Leer datos
        $data = '';
        $intentos = 0;
        
        while (strlen($data) < 8 && $intentos < 10) {
            $chunk = fread($handle, 8 - strlen($data));
            if ($chunk !== false && $chunk !== '') {
                $data .= $chunk;
            }
            $intentos++;
            if (strlen($data) < 8) {
                usleep(50000); // 50ms
            }
        }
        
        fclose($handle);
        
        if (strlen($data) < 7) {
            throw new Exception("Datos incompletos: " . strlen($data) . " bytes");
        }
        
        // Parsear datos
        $estatusByte = ord($data[0]);
        $pesoStr = substr($data, 1, 6);
        $peso = (float)$pesoStr;
        
        if ($estatusByte & 0x08) {
            $peso = -$peso;
        }
        
        $peso = $peso / 100; // Divisor
        
        return [
            'success' => true,
            'peso' => $peso,
            'unidad' => 'kg',
            'estable' => (bool)($estatusByte & 0x04),
            'tipo' => ($estatusByte & 0x01) ? 'neto' : 'bruto',
            'negativo' => (bool)($estatusByte & 0x08),
            'timestamp' => date('Y-m-d H:i:s'),
            'datos_raw' => bin2hex($data)
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

function probarConexion($puerto) {
    $handle = @fopen($puerto . ':', "r+b");
    if ($handle === false) {
        return [
            'success' => false,
            'mensaje' => "Puerto {$puerto} no disponible"
        ];
    }
    fclose($handle);
    return [
        'success' => true,
        'mensaje' => "Puerto {$puerto} accesible",
        'puerto' => $puerto
    ];
}

// Routing simple
switch ($accion) {
    case 'leer':
        echo json_encode(leerBalanza($puerto));
        break;
        
    case 'probar':
        echo json_encode(probarConexion($puerto));
        break;
        
    case 'health':
        echo json_encode([
            'success' => true,
            'mensaje' => 'Gateway funcionando',
            'version' => '1.0',
            'servidor' => gethostname()
        ]);
        break;
        
    default:
        echo json_encode([
            'success' => false,
            'error' => 'Acción no válida'
        ]);
}
```

### Paso 2: Ejecutar el gateway local

En tu PC Windows, abrir PowerShell y ejecutar:

```powershell
cd C:\gateway-balanza
php -S 0.0.0.0:8080 servidor-balanza.php
```

Esto levanta un servidor HTTP en el puerto 8080 de tu PC local.

### Paso 3: Exponer a Internet

Tienes varias opciones:

#### Opción A: ngrok (Gratis/Fácil) ⭐ RECOMENDADO
```powershell
# Descargar ngrok de https://ngrok.com/download
ngrok http 8080
```

Te dará una URL pública tipo: `https://abc123.ngrok.io`

#### Opción B: Configurar port forwarding en tu router
- Abrir puerto 8080 en tu router
- Apuntar a la IP local de tu PC
- Usar tu IP pública o un DNS dinámico (No-IP, DuckDNS)

#### Opción C: Cloudflare Tunnel (Gratis)
```powershell
cloudflared tunnel --url http://localhost:8080
```

---

## 🔌 Modificar Laravel para usar el Gateway

### Actualizar BalanzaService.php

```php
<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BalanzaService
{
    protected $gatewayUrl;
    protected $puerto;

    public function __construct()
    {
        // URL del gateway (configurar en .env)
        $this->gatewayUrl = config('balanza.gateway_url');
        $this->puerto = config('balanza.puerto', 'COM1');
    }

    public function leerPeso(): array
    {
        try {
            $response = Http::timeout(5)->get($this->gatewayUrl, [
                'accion' => 'leer',
                'puerto' => $this->puerto
            ]);

            if ($response->failed()) {
                throw new Exception("Error al conectar con gateway: " . $response->status());
            }

            $data = $response->json();

            if (!$data['success']) {
                throw new Exception($data['error'] ?? 'Error desconocido');
            }

            return [
                'peso' => $data['peso'],
                'unidad' => $data['unidad'] ?? 'kg',
                'estatus' => [
                    'equilibrio' => $data['estable'],
                    'tipo_peso' => $data['tipo'],
                    'negativo' => $data['negativo']
                ]
            ];

        } catch (Exception $e) {
            Log::error("Error leyendo peso desde gateway", [
                'error' => $e->getMessage(),
                'gateway' => $this->gatewayUrl
            ]);
            throw $e;
        }
    }

    public function probarConexion(): array
    {
        try {
            $response = Http::timeout(3)->get($this->gatewayUrl, [
                'accion' => 'probar',
                'puerto' => $this->puerto
            ]);

            return $response->json();

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
```

### Actualizar config/balanza.php

```php
<?php

return [
    // URL del gateway local (expuesta por ngrok o similar)
    'gateway_url' => env('BALANZA_GATEWAY_URL', 'http://localhost:8080/servidor-balanza.php'),
    
    // Puerto COM en el gateway
    'puerto' => env('BALANZA_PUERTO', 'COM1'),
    
    // Configuración original (por si vuelves a servidor con acceso directo)
    'baud_rate' => env('BALANZA_BAUD_RATE', 9600),
    'divisor' => env('BALANZA_DIVISOR', 100),
];
```

### Actualizar .env en producción

```env
# Gateway remoto (ngrok o tu IP pública)
BALANZA_GATEWAY_URL=https://abc123.ngrok.io

# Puerto COM en la PC local
BALANZA_PUERTO=COM1
```

---

## 🚀 Ventajas de esta Solución

✅ **No requiere cambios en el hosting** - Ferozo no necesita hacer nada
✅ **Funciona con hosting compartido** - Solo usa HTTP
✅ **Fácil de mantener** - Código simple en PHP
✅ **Escalable** - Puedes tener múltiples balanzas
✅ **Seguro** - Puedes agregar autenticación (API key)
✅ **Debugging fácil** - Logs en ambos lados

---

## 🔒 Seguridad

### Agregar autenticación al gateway

```php
// Al inicio de servidor-balanza.php
$apiKey = 'tu-clave-secreta-aqui'; // Cambiar por una clave segura

if (!isset($_GET['api_key']) || $_GET['api_key'] !== $apiKey) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}
```

En .env de producción:
```env
BALANZA_GATEWAY_URL=https://abc123.ngrok.io?api_key=tu-clave-secreta-aqui
```

---

## 🧪 Testing

### 1. Probar gateway local
```
http://localhost:8080/servidor-balanza.php?accion=health
```

### 2. Probar desde Internet (con ngrok)
```
https://abc123.ngrok.io?accion=health
```

### 3. Probar lectura
```
https://abc123.ngrok.io?accion=leer&puerto=COM1
```

---

## 💰 Costos

- **ngrok Free**: Gratis (URL cambia cada vez que reinicias)
- **ngrok Pro**: USD $8/mes (URL fija)
- **Cloudflare Tunnel**: Gratis
- **Port forwarding**: Gratis (requiere IP fija o DNS dinámico)

---

## 📝 Checklist de Implementación

- [ ] Crear `C:\gateway-balanza\servidor-balanza.php`
- [ ] Probar gateway local: `php -S 0.0.0.0:8080 servidor-balanza.php`
- [ ] Instalar ngrok
- [ ] Ejecutar: `ngrok http 8080`
- [ ] Copiar URL pública de ngrok
- [ ] Actualizar BalanzaService.php
- [ ] Actualizar config/balanza.php
- [ ] Configurar .env con BALANZA_GATEWAY_URL
- [ ] Subir cambios a producción
- [ ] Probar desde https://balanza.barloventosrl.website

---

## ⚡ Bonus: Ejecutar Gateway como Servicio Windows

Para que el gateway se ejecute automáticamente al iniciar Windows:

1. Descargar NSSM: https://nssm.cc/download
2. Ejecutar:
```powershell
nssm install BalanzaGateway "C:\wamp64\bin\php\php8.2.0\php.exe" "-S" "0.0.0.0:8080" "C:\gateway-balanza\servidor-balanza.php"
nssm start BalanzaGateway
```

Ahora el gateway se ejecutará siempre en segundo plano.
