<?php
/**
 * Script de prueba directo para la balanza
 * Acceder desde: https://balanza.barloventosrl.website/test-directo.php
 * 
 * Este script prueba la lectura directa sin pasar por Laravel
 */

header('Content-Type: text/html; charset=utf-8');

// Detectar sistema operativo
$isWindows = (PHP_OS_FAMILY === 'Windows');
$defaultPort = $isWindows ? 'COM1' : '/dev/ttyUSB0';

$puerto = $_GET['puerto'] ?? $defaultPort;
$resultado = [];

echo "<h1>🔬 Prueba Directa de Balanza</h1>";
echo "<p>Sistema Operativo: <strong>" . PHP_OS_FAMILY . "</strong></p>";
echo "<p>Puerto: <strong>{$puerto}</strong></p>";

// Verificar restricción open_basedir
$openBasedir = ini_get('open_basedir');
if ($openBasedir) {
    echo "<p>⚠️ <strong>open_basedir activo:</strong> <code>" . htmlspecialchars($openBasedir) . "</code></p>";
    if (strpos($puerto, '/dev/') === 0 && strpos($openBasedir, '/dev') === false) {
        echo "<p style='color: red;'>❌ <strong>PROBLEMA:</strong> El directorio /dev NO está incluido en open_basedir.</p>";
        echo "<p>Debe solicitar a su proveedor de hosting que agregue <code>/dev</code> a la directiva open_basedir.</p>";
        echo "<p>📄 <a href='../SOLICITUD_HOSTING_FEROZO.md' target='_blank'>Ver modelo de solicitud para Ferozo</a></p>";
    }
}

echo "<hr>";

try {
    // Intentar abrir el puerto
    echo "<p>📡 Intentando abrir puerto {$puerto}...</p>";
    
    // En Windows agregar ":" al final, en Linux no
    $portPath = $isWindows ? $puerto . ':' : $puerto;
    
    $handle = @fopen($portPath, "r+b");
    
    if ($handle === false) {
        throw new Exception("No se pudo abrir el puerto {$puerto}. Error: " . error_get_last()['message'] ?? 'desconocido');
    }
    
    echo "<p>✅ Puerto abierto correctamente</p>";
    
    // Configurar timeout
    stream_set_timeout($handle, 2);
    echo "<p>⏱️ Timeout configurado: 2 segundos</p>";
    
    // Limpiar buffer
    stream_set_blocking($handle, false);
    $cleaned = '';
    while ($chunk = fread($handle, 1024)) {
        $cleaned .= $chunk;
    }
    if ($cleaned) {
        echo "<p>🧹 Buffer limpiado: " . strlen($cleaned) . " bytes descartados</p>";
    }
    stream_set_blocking($handle, true);
    
    // Leer datos
    echo "<p>📥 Leyendo datos...</p>";
    $data = '';
    $attempts = 0;
    $maxAttempts = 5;
    
    while (strlen($data) < 8 && $attempts < $maxAttempts) {
        $chunk = fread($handle, 8 - strlen($data));
        if ($chunk !== false && $chunk !== '') {
            $data .= $chunk;
            echo "<p>📊 Intento " . ($attempts + 1) . ": Recibidos " . strlen($chunk) . " bytes</p>";
        }
        $attempts++;
        if (strlen($data) < 8) {
            usleep(100000); // 100ms
        }
    }
    
    fclose($handle);
    
    if (strlen($data) < 7) {
        throw new Exception("Datos insuficientes. Se recibieron " . strlen($data) . " bytes, se esperaban al menos 7");
    }
    
    echo "<p>✅ Datos recibidos: " . strlen($data) . " bytes</p>";
    
    // Mostrar datos en diferentes formatos
    echo "<h2>📊 Datos Recibidos</h2>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Formato</th><th>Valor</th></tr>";
    echo "<tr><td>ASCII</td><td><code>" . htmlspecialchars($data) . "</code></td></tr>";
    echo "<tr><td>Hexadecimal</td><td><code>" . bin2hex($data) . "</code></td></tr>";
    echo "<tr><td>Longitud</td><td>" . strlen($data) . " bytes</td></tr>";
    echo "</table>";
    
    // Parsear datos
    echo "<h2>🔍 Análisis de Datos</h2>";
    
    $estatusByte = ord($data[0]);
    $pesoStr = substr($data, 1, 6);
    
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Campo</th><th>Valor</th></tr>";
    echo "<tr><td>Byte de Estatus (decimal)</td><td>" . $estatusByte . "</td></tr>";
    echo "<tr><td>Byte de Estatus (binario)</td><td>" . sprintf('%08b', $estatusByte) . "</td></tr>";
    echo "<tr><td>Byte de Estatus (hex)</td><td>0x" . dechex($estatusByte) . "</td></tr>";
    echo "<tr><td>Peso (string)</td><td>{$pesoStr}</td></tr>";
    echo "<tr><td>Peso (numérico)</td><td>" . (float)$pesoStr . "</td></tr>";
    echo "</table>";
    
    // Decodificar estatus
    echo "<h2>📋 Estado de la Balanza</h2>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Bit</th><th>Significado</th><th>Valor</th></tr>";
    echo "<tr><td>0</td><td>Tipo de peso</td><td>" . (($estatusByte & 0x01) ? '🟢 NETO' : '📦 BRUTO') . "</td></tr>";
    echo "<tr><td>1</td><td>Centro de cero</td><td>" . (($estatusByte & 0x02) ? '✅ Sí' : '❌ No') . "</td></tr>";
    echo "<tr><td>2</td><td>Equilibrio</td><td>" . (($estatusByte & 0x04) ? '✅ ESTABLE' : '⚠️ EN MOVIMIENTO') . "</td></tr>";
    echo "<tr><td>3</td><td>Signo</td><td>" . (($estatusByte & 0x08) ? '➖ Negativo' : '➕ Positivo') . "</td></tr>";
    echo "<tr><td>4</td><td>Rango</td><td>" . (($estatusByte & 0x10) ? '❌ FUERA DE RANGO' : '✅ Normal') . "</td></tr>";
    echo "</table>";
    
    // Calcular peso final
    $peso = (float)$pesoStr;
    if ($estatusByte & 0x08) {
        $peso = -$peso;
    }
    $peso = $peso / 100; // Divisor por defecto
    
    echo "<h2>⚖️ Peso Final</h2>";
    echo "<p style='font-size: 48px; color: green;'><strong>{$peso} kg</strong></p>";
    
    echo "<hr>";
    echo "<p>✅ <strong>Prueba exitosa</strong></p>";
    
    if ($isWindows) {
        echo "<p><a href='?puerto=COM1'>Probar COM1</a> | ";
        echo "<a href='?puerto=COM2'>Probar COM2</a> | ";
        echo "<a href='?puerto=COM3'>Probar COM3</a> | ";
        echo "<a href='?puerto=COM4'>Probar COM4</a></p>";
    } else {
        echo "<p><a href='?puerto=/dev/ttyUSB0'>Probar /dev/ttyUSB0</a> | ";
        echo "<a href='?puerto=/dev/ttyUSB1'>Probar /dev/ttyUSB1</a> | ";
        echo "<a href='?puerto=/dev/ttyS0'>Probar /dev/ttyS0</a> | ";
        echo "<a href='?puerto=/dev/ttyS1'>Probar /dev/ttyS1</a></p>";
    }
    
} catch (Exception $e) {
    echo "<h2>❌ Error</h2>";
    echo "<p style='color: red;'><strong>" . htmlspecialchars($e->getMessage()) . "</strong></p>";
    echo "<p>Verifica:</p>";
    echo "<ul>";
    echo "<li>Que la balanza esté conectada y encendida</li>";
    echo "<li>Que el puerto COM sea el correcto</li>";
    echo "<li>Que ningún otro programa esté usando el puerto</li>";
    echo "<li>Que la balanza esté configurada para transmitir datos</li>";
    
    if (!$isWindows && strpos($puerto, '/dev/') === 0) {
        echo "<li><strong>En Linux:</strong> Verificar permisos del puerto (sudo chmod 666 {$puerto})</li>";
        echo "<li><strong>En Linux:</strong> Agregar usuario al grupo dialout (sudo usermod -a -G dialout nombreusuario)</li>";
        if ($openBasedir && strpos($openBasedir, '/dev') === false) {
            echo "<li style='color: red;'><strong>CRÍTICO:</strong> Solicitar a hosting que agregue /dev a open_basedir</li>";
        }
    }
    
    echo "</ul>";
    
    if ($isWindows) {
        echo "<p><a href='?puerto=COM1'>Probar COM1</a> | ";
        echo "<a href='?puerto=COM2'>Probar COM2</a> | ";
        echo "<a href='?puerto=COM3'>Probar COM3</a> | ";
        echo "<a href='?puerto=COM4'>Probar COM4</a></p>";
    } else {
        echo "<p><a href='?puerto=/dev/ttyUSB0'>Probar /dev/ttyUSB0</a> | ";
        echo "<a href='?puerto=/dev/ttyUSB1'>Probar /dev/ttyUSB1</a> | ";
        echo "<a href='?puerto=/dev/ttyS0'>Probar /dev/ttyS0</a> | ";
        echo "<a href='?puerto=/dev/ttyS1'>Probar /dev/ttyS1</a></p>";
    }
}
