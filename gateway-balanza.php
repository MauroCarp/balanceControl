<?php
/**
 * Gateway para comunicación con el servidor Python local de la balanza
 */

// Habilitar debugging temporalmente
ini_set('display_errors', '1');
error_reporting(E_ALL);

// Verificar que PHP se está ejecutando
logMessage("Gateway iniciando - PHP Version: " . PHP_VERSION);
logMessage("REQUEST_URI: " . $_SERVER['REQUEST_URI']);
logMessage("QUERY_STRING: " . $_SERVER['QUERY_STRING']);
logMessage("GET params: " . json_encode($_GET));

// Configurar headers después de la verificación
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
set_time_limit(10);

// Configuración del servidor local
define('PYTHON_SERVER_URL', 'http://127.0.0.1:5000');
define('DEBUG', true);

// Función de logging
function logMessage($message, $type = 'INFO') {
    $logFile = __DIR__ . '/gateway_debug.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] [$type] $message\n";
    
    // Intentar escribir el log
    if (!file_put_contents($logFile, $logMessage, FILE_APPEND)) {
        error_log("No se pudo escribir en el archivo de log: $logFile");
    }
    
    if (DEBUG) {
        error_log("Gateway log ($logFile): $logMessage");
    }
    
    // Si es el primer mensaje, registrar información del sistema
    if (!file_exists($logFile) || filesize($logFile) === 0) {
        $sysInfo = [
            'Fecha' => date('Y-m-d H:i:s'),
            'PHP Version' => PHP_VERSION,
            'Sistema Operativo' => PHP_OS,
            'Directorio del Gateway' => __DIR__,
            'Usuario PHP' => get_current_user(),
            'Archivo de Log' => $logFile,
            'Permisos de escritura' => is_writable(__DIR__) ? 'Sí' : 'No'
        ];
        
        $sysInfoLog = "\n=== Información del Sistema ===\n";
        foreach ($sysInfo as $key => $value) {
            $sysInfoLog .= "$key: $value\n";
        }
        $sysInfoLog .= "===========================\n\n";
        
        file_put_contents($logFile, $sysInfoLog, FILE_APPEND);
    }
}

// Debug function
function debug($message, $data = null) {
    $logFile = __DIR__ . '/balanza_debug.log';
    $logMessage = sprintf(
        "[%s] %s %s\n", 
        date('Y-m-d H:i:s'),
        $message,
        $data !== null ? json_encode($data, JSON_PRETTY_PRINT) : ''
    );
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// Definir valores por defecto para la balanza EL05B
$defaultConfig = [
    'puerto' => 'COM1',
    'baudRate' => 9600,    // Velocidad recomendada para EL05B
    'dataBits' => 8,       // 8 bits de datos
    'parity' => 'N',       // Sin paridad
    'stopBits' => 1,       // 1 bit de parada
    'timeout' => 2,
    'maxIntentos' => 5,
    'delayMs' => 50,       // 50ms entre intentos
    'formato' => 'e105'    // Formato específico de la EL05B
];

// Cargar configuración desde .env o usar valores por defecto
$config = [
    'puerto' => $_GET['puerto'] ?? $defaultConfig['puerto'],
    'baudRate' => (int)(getenv('BALANZA_BAUD_RATE')) ?: $defaultConfig['baudRate'],
    'dataBits' => (int)(getenv('BALANZA_DATA_BITS')) ?: $defaultConfig['dataBits'],
    'parity' => getenv('BALANZA_PARITY') ?: $defaultConfig['parity'],
    'stopBits' => (int)(getenv('BALANZA_STOP_BITS')) ?: $defaultConfig['stopBits'],
    'timeout' => (int)(getenv('BALANZA_TIMEOUT')) ?: $defaultConfig['timeout'],
    'maxIntentos' => (int)(getenv('BALANZA_MAX_INTENTOS')) ?: $defaultConfig['maxIntentos'],
    'delayMs' => (int)(getenv('BALANZA_DELAY_MS')) ?: $defaultConfig['delayMs'],
];

// Validar la configuración
if ($config['baudRate'] <= 0) $config['baudRate'] = $defaultConfig['baudRate'];
if ($config['dataBits'] <= 0) $config['dataBits'] = $defaultConfig['dataBits'];
if ($config['stopBits'] <= 0) $config['stopBits'] = $defaultConfig['stopBits'];
if (empty($config['parity'])) $config['parity'] = $defaultConfig['parity'];
if ($config['timeout'] <= 0) $config['timeout'] = $defaultConfig['timeout'];
if ($config['maxIntentos'] <= 0) $config['maxIntentos'] = $defaultConfig['maxIntentos'];
if ($config['delayMs'] <= 0) $config['delayMs'] = $defaultConfig['delayMs'];

debug("Configuración cargada", $config);

function obtenerPuertosDisponibles() {
    $puertos = [];
    
    // Método 1: Intentar usando PowerShell
    $cmd = 'powershell.exe -Command "[System.IO.Ports.SerialPort]::getportnames()"';
    @exec($cmd, $output, $returnCode);
    
    if ($returnCode === 0) {
        foreach ($output as $line) {
            $puerto = trim($line);
            if (preg_match('/^COM\d+$/', $puerto)) {
                $puertos[] = $puerto;
            }
        }
    }
    
    // Método 2: Si PowerShell falló o no encontró puertos, intentar método tradicional
    if (empty($puertos) && strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        for ($i = 1; $i <= 9; $i++) {
            $testPuerto = "COM" . $i;
            $testHandle = @fopen($testPuerto . ":", "r+b");
            if ($testHandle) {
                $puertos[] = $testPuerto;
                fclose($testHandle);
            }
        }
    }
    
    return $puertos;
}

function verificarPuerto($puerto) {
    // Obtener lista de puertos disponibles
    $puertos = obtenerPuertosDisponibles();

    // Verificar si el puerto solicitado existe
    if (!in_array($puerto, $puertos)) {
        $mensaje = "Puerto {$puerto} no detectado en el sistema. ";
        if (!empty($puertos)) {
            $mensaje .= "Puertos disponibles: " . implode(", ", $puertos);
        } else {
            $mensaje .= "No se detectaron puertos COM accesibles. Verifique que:
            1. El dispositivo esté conectado
            2. Los drivers estén instalados
            3. Esté ejecutando el script como administrador
            4. PowerShell esté disponible y tenga permisos de ejecución";
        }
        throw new Exception($mensaje);
    }

    // Intentar abrir el puerto para verificar acceso
    $handle = @fopen($puerto . ":", "r+b");
    if (!$handle) {
        throw new Exception("El puerto {$puerto} existe pero no se puede acceder. Puede estar siendo usado por otra aplicación o no tiene permisos suficientes.");
    }
    fclose($handle);
    
    if (!in_array($puerto, $puertos)) {
        throw new Exception(
            "Puerto {$puerto} no detectado en el sistema. " .
            "Puertos disponibles: " . implode(", ", $puertos)
        );
    }
    
    // Verificar si el puerto está en uso
    $handle = @fopen($puerto . ":", "r+b");
    if (!$handle) {
        throw new Exception(
            "Puerto {$puerto} existe pero no se puede acceder. " .
            "Puede estar en uso por otra aplicación o no tener permisos suficientes."
        );
    }
    fclose($handle);
}

function leerBalanza($config) {
    try {
        debug("Iniciando lectura de balanza a través del servidor local", $config);
        
        // URL del servidor Python local
        $serverUrl = "http://127.0.0.1:5000";
        
        // Primero verificar si el servidor está activo
        debug("Verificando estado del servidor local");
        
        $ch = curl_init($serverUrl . '/status');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 3,
            CURLOPT_CONNECTTIMEOUT => 2
        ]);
        
        $statusResponse = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if ($statusCode !== 200 || $statusResponse === false) {
            throw new Exception(
                "No se pudo conectar con el servidor local de la balanza. " .
                "Asegúrese que el servidor Python esté ejecutándose. " .
                "Error: " . curl_error($ch)
            );
        }
        
        curl_close($ch);
        
        debug("Servidor local activo, solicitando lectura de peso");
        
        // Realizar la solicitud al endpoint de peso
        $ch = curl_init($serverUrl . '/peso');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_CONNECTTIMEOUT => 3
        ]);
        
        $timeStart = microtime(true);
        $response = curl_exec($ch);
        $tiempoTotal = microtime(true) - $timeStart;
        
        if ($response === false) {
            throw new Exception("Error al leer datos del servidor local: " . curl_error($ch));
        }
        
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($statusCode !== 200) {
            throw new Exception("El servidor respondió con código de error: " . $statusCode);
        }
        
        curl_close($ch);
        
        // Decodificar la respuesta JSON
        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error al decodificar la respuesta JSON: " . json_last_error_msg());
        }
        
        debug("Datos recibidos del servidor local", $data);
        
        // Verificar si hay lecturas válidas
        if (empty($data['lecturas'])) {
            throw new Exception("No se obtuvieron lecturas válidas de la balanza");
        }
        
        // Buscar la primera lectura válida y estable
        $lecturaValida = null;
        foreach ($data['lecturas'] as $lectura) {
            if ($lectura['valido'] && $lectura['estable']) {
                $lecturaValida = $lectura;
                break;
            }
        }
        
        if ($lecturaValida === null) {
            throw new Exception("No se encontró una lectura válida y estable");
        }
        
        return [
            'success' => true,
            'peso' => $lecturaValida['peso_kg'],
            'unidad' => 'kg',
            'estable' => $lecturaValida['estable'],
            'estado' => [
                'estable' => $lecturaValida['estable'],
                'cero' => $lecturaValida['estatus']['centro_cero'],
                'negativo' => $lecturaValida['estatus']['negativo'],
                'sobrecarga' => $lecturaValida['estatus']['fuera_rango']
            ],
            'tiempo_respuesta' => round($tiempoTotal, 3),
            'datos_origen' => $data
        ];
    } catch (Exception $e) {
        return [
            'success' => false, 
            'error' => $e->getMessage(),
            'detalles' => [
                'php_version' => PHP_VERSION,
                'os' => PHP_OS,
                'tiempo_max_ejecucion' => ini_get('max_execution_time')
            ]
        ];
    }
}

// Función para hacer peticiones al servidor Python
function callPythonServer($endpoint, $params = []) {
    $url = PYTHON_SERVER_URL . $endpoint;
    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }
    
    logMessage("Llamando a: $url");
    
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 5,
        CURLOPT_CONNECTTIMEOUT => 3,
        CURLOPT_FAILONERROR => true
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        logMessage("Error CURL: $error", 'ERROR');
        throw new Exception("Error de conexión con el servidor local: $error");
    }
    
    curl_close($ch);
    
    if ($httpCode !== 200) {
        logMessage("Error HTTP $httpCode: $response", 'ERROR');
        throw new Exception("El servidor respondió con código: $httpCode");
    }
    
    logMessage("Respuesta recibida: $response");
    return $response;
}

// Manejar la petición
try {
    $endpoint = '';
    $params = [];
    
    // Determinar el endpoint basado en el parámetro de acción
    switch ($_GET['action'] ?? 'peso') {
        case 'status':
            $endpoint = '/status';
            break;
            
        case 'diagnostico':
            $endpoint = '/diagnostico';
            break;
            
        case 'peso':
        default:
            $endpoint = '/peso';
            $params['puerto'] = $_GET['puerto'] ?? 'COM1';
            $params['baudrate'] = $_GET['baudrate'] ?? 1200;
            break;
    }
    
    // Llamar al servidor Python
    $response = callPythonServer($endpoint, $params);
    
    // Verificar que la respuesta sea JSON válido
    $data = json_decode($response);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Respuesta inválida del servidor: " . json_last_error_msg());
    }
    
    // Enviar respuesta al cliente
    echo $response;
    
} catch (Throwable $e) {
    logMessage("Error: " . $e->getMessage(), 'ERROR');
    
    $error = [
        'success' => false,
        'error' => $e->getMessage(),
        'timestamp' => date('c'),
        'debug_info' => [
            'php_version' => PHP_VERSION,
            'gateway_version' => '1.1.0',
            'python_server' => PYTHON_SERVER_URL
        ]
    ];
    
    echo json_encode($error, JSON_PRETTY_PRINT);
}
