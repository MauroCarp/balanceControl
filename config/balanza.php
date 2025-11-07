<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Configuración de la Balanza Digital
    |--------------------------------------------------------------------------
    |
    | Configuración simplificada usando gateway HTTP
    |
    */

    // URL del gateway HTTP que lee la balanza en tu PC local
    // Ejemplo con ngrok: https://abc123.ngrok.io/gateway-balanza.php
    // Ejemplo local: http://localhost:8080/gateway-balanza.php
    'gateway_url' => env('BALANZA_GATEWAY_URL', 'http://localhost:8080/gateway-balanza.php'),

    // Puerto COM en tu PC local
    'puerto' => env('BALANZA_PUERTO', 'COM1'),

];
