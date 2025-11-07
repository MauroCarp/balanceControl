<?php
/**
 * Gateway Serial-to-HTTP para Balanza EL05B
 * 
 * Este script se ejecuta en la PC local donde está conectada la balanza
 * Expone una API HTTP que puede ser consultada desde Internet
 * 
 * Uso: php -S 0.0.0.0:8080 servidor-balanza.php
 * 
 * Endpoints:
 * - ?accion=leer&puerto=COM1 - Lee peso actual
 * - ?accion=probar&puerto=COM1 - Prueba conexión
 * - ?accion=health - Estado del servidor
 */

// Configuración
define('API_KEY', 'cambiar-por-clave-segura'); // ⚠️ CAMBIAR ESTA CLAVE
define('REQUIRE_API_KEY', false); // Cambiar a true para requerir autenticación

// Headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Manejar preflight CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Log de solicitudes
error_log(sprintf(
    "[%s] Gateway Request: %s from %s",
    date('Y-m-d H:i:s'),
    $_SERVER['REQUEST_URI'] ?? '',
    $_SERVER['REMOTE_ADDR'] ?? 'unknown'
));

// Verificar API Key si está habilitada
if (REQUIRE_API_KEY) {
    $providedKey = $_GET['api_key'] ?? $_SERVER['HTTP_X_API_KEY'] ?? '';
    if ($providedKey !== API_KEY) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'error' => 'No autorizado - API Key inválida'
        ]);
        exit;
    }
}

// Parámetros
$puerto = $_GET['puerto'] ?? 'COM1';
$accion = $_GET['accion'] ?? 'leer';

/**
 * Lee datos de la balanza
 */
function leerBalanza($puerto) {
    $startTime = microtime(true);
    
    try {
        // Abrir puerto
        $handle = @fopen($puerto . ':', "r+b");
        
        if ($handle === false) {
            $error = error_get_last();
            throw new Exception("No se pudo abrir puerto {$puerto}: " . ($error['message'] ?? 'desconocido'));
        }
        
        // Configurar timeout
        stream_set_timeout($handle, 2);
        
        // Limpiar buffer
        stream_set_blocking($handle, false);
        $limpiado = '';
        while ($chunk = fread($handle, 1024)) {
            $limpiado .= $chunk;
        }
        stream_set_blocking($handle, true);
        
        // Leer datos
        $data = '';
        $intentos = 0;
        $maxIntentos = 10;
        
        while (strlen($data) < 8 && $intentos < $maxIntentos) {
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
            throw new Exception("Datos incompletos: " . strlen($data) . " bytes recibidos, se esperaban 7");
        }
        
        // Parsear datos según formato e105
        $estatusByte = ord($data[0]);
        $pesoStr = substr($data, 1, 6);
        
        // Validar que el peso sea numérico
        if (!ctype_digit($pesoStr)) {
            throw new Exception("Peso no numérico recibido: {$pesoStr}");
        }
        
        $peso = (float)$pesoStr;
        
        // Aplicar signo
        if ($estatusByte & 0x08) {
            $peso = -$peso;
        }
        
        // Aplicar divisor (100 = 2 decimales)
        $peso = $peso / 100;
        
        $duracion = round((microtime(true) - $startTime) * 1000, 2);
        
        return [
            'success' => true,
            'peso' => $peso,
            'unidad' => 'kg',
            'estable' => (bool)($estatusByte & 0x04),
            'tipo' => ($estatusByte & 0x01) ? 'neto' : 'bruto',
            'negativo' => (bool)($estatusByte & 0x08),
            'fuera_rango' => (bool)($estatusByte & 0x10),
            'centro_cero' => (bool)($estatusByte & 0x02),
            'timestamp' => date('Y-m-d H:i:s'),
            'datos_raw' => bin2hex($data),
            'puerto' => $puerto,
            'intentos' => $intentos,
            'duracion_ms' => $duracion,
            'buffer_limpiado' => strlen($limpiado) . ' bytes'
        ];
        
    } catch (Exception $e) {
        error_log("Error leyendo balanza: " . $e->getMessage());
        return [
            'success' => false,
            'error' => $e->getMessage(),
            'puerto' => $puerto
        ];
    }
}

/**
 * Prueba la conexión al puerto
 */
function probarConexion($puerto) {
    try {
        $handle = @fopen($puerto . ':', "r+b");
        
        if ($handle === false) {
            $error = error_get_last();
            return [
                'success' => false,
                'mensaje' => "Puerto {$puerto} no disponible",
                'error' => $error['message'] ?? 'desconocido'
            ];
        }
        
        fclose($handle);
        
        return [
            'success' => true,
            'mensaje' => "Puerto {$puerto} accesible",
            'puerto' => $puerto
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Listar puertos COM disponibles (Windows)
 */
function listarPuertos() {
    $puertos = [];
    
    // En Windows, intentar abrir COM1 a COM9
    for ($i = 1; $i <= 9; $i++) {
        $puerto = "COM{$i}";
        $handle = @fopen($puerto . ':', "r");
        if ($handle !== false) {
            fclose($handle);
            $puertos[] = $puerto;
        }
    }
    
    return [
        'success' => true,
        'puertos' => $puertos,
        'total' => count($puertos)
    ];
}

/**
 * Estado del gateway
 */
function health() {
    return [
        'success' => true,
        'mensaje' => 'Gateway funcionando correctamente',
        'version' => '1.0.0',
        'servidor' => gethostname(),
        'php_version' => PHP_VERSION,
        'sistema' => PHP_OS_FAMILY,
        'timestamp' => date('Y-m-d H:i:s'),
        'memoria_mb' => round(memory_get_usage() / 1024 / 1024, 2),
        'autenticacion' => REQUIRE_API_KEY ? 'habilitada' : 'deshabilitada'
    ];
}

// Routing
try {
    switch ($accion) {
        case 'leer':
            $resultado = leerBalanza($puerto);
            break;
            
        case 'probar':
            $resultado = probarConexion($puerto);
            break;
            
        case 'listar':
            $resultado = listarPuertos();
            break;
            
        case 'health':
            $resultado = health();
            break;
            
        default:
            http_response_code(400);
            $resultado = [
                'success' => false,
                'error' => 'Acción no válida',
                'acciones_disponibles' => ['leer', 'probar', 'listar', 'health']
            ];
    }
    
    echo json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ], JSON_PRETTY_PRINT);
}
