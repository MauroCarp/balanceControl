<?php

namespace App\Services;

use Exception;

class BalanzaService
{
    protected $port;
    protected $baudRate;
    protected $parity;
    protected $dataBits;
    protected $stopBits;

    public function __construct(
        string $port = 'COM1',
        int $baudRate = 9600,
        string $parity = 'none',
        int $dataBits = 8,
        int $stopBits = 1
    ) {
        $this->port = $port;
        $this->baudRate = $baudRate;
        $this->parity = $parity;
        $this->dataBits = $dataBits;
        $this->stopBits = $stopBits;
    }

    /**
     * Lee el peso desde el puerto serie de la balanza
     * 
     * @return array ['peso' => float, 'estatus' => array]
     * @throws Exception
     */
    public function leerPeso(): array
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return $this->leerPesoWindows();
        } else {
            return $this->leerPesoLinux();
        }
    }

    /**
     * Lee el peso en sistemas Windows
     */
    protected function leerPesoWindows(): array
    {
        // Configurar el puerto serie en Windows
        $port = $this->port;
        
        // Configurar el puerto con mode (comando de Windows)
        $config = "mode {$port} BAUD={$this->baudRate} PARITY=N DATA={$this->dataBits} STOP={$this->stopBits}";
        exec($config, $output, $returnVar);

        // Abrir el puerto serie
        $handle = @fopen($port, "r+b");
        
        if ($handle === false) {
            throw new Exception("No se pudo abrir el puerto {$port}. Verifique que la balanza esté conectada.");
        }

        // Configurar timeout de lectura (2 segundos)
        stream_set_timeout($handle, 2);

        // Leer datos del puerto
        $data = fread($handle, 8); // Leer 8 bytes (1 estatus + 6 peso + 1 CR)
        
        fclose($handle);

        if (empty($data)) {
            throw new Exception("No se recibieron datos de la balanza. Verifique la conexión.");
        }

        return $this->parsearDatos($data);
    }

    /**
     * Lee el peso en sistemas Linux
     */
    protected function leerPesoLinux(): array
    {
        $port = str_replace('COM', '/dev/ttyS', $this->port);
        
        // Configurar el puerto serie en Linux
        exec("stty -F {$port} {$this->baudRate} cs{$this->dataBits} -cstopb -parenb", $output, $returnVar);

        // Abrir el puerto serie
        $handle = @fopen($port, "r+");
        
        if ($handle === false) {
            throw new Exception("No se pudo abrir el puerto {$port}");
        }

        // Configurar timeout
        stream_set_timeout($handle, 2);

        // Leer datos
        $data = fread($handle, 8);
        
        fclose($handle);

        if (empty($data)) {
            throw new Exception("No se recibieron datos de la balanza");
        }

        return $this->parsearDatos($data);
    }

    /**
     * Parsea los datos recibidos de la balanza según el formato e105
     * 
     * Formato: <estatus><peso><CR>
     * - estatus: 1 byte con información de estado
     * - peso: 6 caracteres sin punto decimal
     * - CR: 0x0D
     */
    protected function parsearDatos(string $data): array
    {
        // Verificar que tengamos al menos 8 bytes
        if (strlen($data) < 7) {
            throw new Exception("Datos incompletos recibidos de la balanza");
        }

        // Extraer el byte de estatus (primer byte)
        $estatusByte = ord($data[0]);

        // Extraer el peso (6 caracteres)
        $pesoStr = substr($data, 1, 6);
        $peso = (float) $pesoStr;

        // Decodificar el byte de estatus según el formato e105
        $estatus = [
            'tipo_peso' => ($estatusByte & 0x01) ? 'neto' : 'bruto',
            'centro_cero' => (bool) ($estatusByte & 0x02),
            'equilibrio' => (bool) ($estatusByte & 0x04),
            'negativo' => (bool) ($estatusByte & 0x08),
            'fuera_rango' => (bool) ($estatusByte & 0x10),
        ];

        // Si el peso es negativo, convertirlo
        if ($estatus['negativo']) {
            $peso = -$peso;
        }

        // Aplicar divisor decimal (asumiendo 2 decimales, ajustar según configuración de tu balanza)
        $peso = $peso / 100;

        return [
            'peso' => $peso,
            'estatus' => $estatus,
            'peso_formateado' => number_format($peso, 2, '.', ''),
            'en_equilibrio' => $estatus['equilibrio'],
            'valido' => !$estatus['fuera_rango'] && $estatus['equilibrio'],
        ];
    }

    /**
     * Espera hasta que el peso esté estable
     */
    public function esperarPesoEstable(int $maxIntentos = 10, int $delayMs = 500): array
    {
        $intentos = 0;
        
        while ($intentos < $maxIntentos) {
            $resultado = $this->leerPeso();
            
            if ($resultado['en_equilibrio']) {
                return $resultado;
            }
            
            usleep($delayMs * 1000); // Convertir ms a microsegundos
            $intentos++;
        }

        throw new Exception("No se pudo obtener un peso estable después de {$maxIntentos} intentos");
    }
}
