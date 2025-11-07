<?php
/**
 * Script de prueba simple para verificar la ruta de la API
 * Acceder desde: http://localhost/balanceControl/public/test-api.php
 */

header('Content-Type: application/json');

echo json_encode([
    'status' => 'OK',
    'message' => 'PHP está funcionando correctamente',
    'current_url' => $_SERVER['REQUEST_URI'] ?? 'N/A',
    'script_name' => $_SERVER['SCRIPT_NAME'] ?? 'N/A',
    'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'N/A',
    'api_should_be' => '/balanceControl/public/api/balanza/probar-conexion',
    'test_urls' => [
        'Con public' => 'http://localhost/balanceControl/public/api/balanza/probar-conexion',
        'Sin public' => 'http://localhost/balanceControl/api/balanza/probar-conexion',
    ]
], JSON_PRETTY_PRINT);
