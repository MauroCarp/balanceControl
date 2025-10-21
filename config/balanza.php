<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Configuración de la Balanza Digital
    |--------------------------------------------------------------------------
    |
    | Configuración para la comunicación con la balanza digital EL05B
    | a través del puerto serie RS232C
    |
    */

    // Puerto serie donde está conectada la balanza
    // Windows: COM1, COM2, COM3, etc.
    // Linux: /dev/ttyS0, /dev/ttyS1, /dev/ttyUSB0, etc.
    'puerto' => env('BALANZA_PUERTO', 'COM1'),

    // Velocidad de transmisión (baud rate)
    // Opciones: 1200, 2400, 4800, 9600
    'baud_rate' => env('BALANZA_BAUD_RATE', 9600),

    // Configuración de bits de datos
    // Opciones: 7, 8
    'data_bits' => env('BALANZA_DATA_BITS', 8),

    // Paridad
    // Opciones: 'none', 'even', 'odd'
    'parity' => env('BALANZA_PARITY', 'none'),

    // Bits de parada
    // Opciones: 1, 2
    'stop_bits' => env('BALANZA_STOP_BITS', 1),

    // Formato de salida esperado
    // Formato e105: <estatus><peso><CR>
    'formato' => env('BALANZA_FORMATO', 'e105'),

    // Número de decimales para el peso
    // Ajustar según la configuración de tu balanza
    'decimales' => env('BALANZA_DECIMALES', 2),

    // Divisor para convertir el peso
    // Si la balanza envía 123456 y el peso real es 1234.56, el divisor es 100
    'divisor' => env('BALANZA_DIVISOR', 100),

    // Timeout de lectura en segundos
    'timeout' => env('BALANZA_TIMEOUT', 2),

    // Máximo de intentos para obtener peso estable
    'max_intentos_estable' => env('BALANZA_MAX_INTENTOS', 10),

    // Delay entre intentos en milisegundos
    'delay_intentos' => env('BALANZA_DELAY_MS', 500),

    // Habilitar/deshabilitar la lectura automática
    'habilitada' => env('BALANZA_HABILITADA', true),

    // Modo de operación
    // 'manual': El usuario hace clic en un botón para leer
    // 'automatico': Lectura automática periódica
    'modo' => env('BALANZA_MODO', 'manual'),

    // Intervalo de lectura automática en milisegundos (solo si modo = 'automatico')
    'intervalo_lectura' => env('BALANZA_INTERVALO_MS', 1000),

];
