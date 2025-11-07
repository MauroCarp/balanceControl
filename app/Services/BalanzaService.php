<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class BalanzaService
{
    protected $gatewayUrl;

    public function __construct()
    {
        $this->gatewayUrl = config('balanza.gateway_url');
    }

    public function leerPeso(): array
    {
        try {
            $response = Http::timeout(5)->get($this->gatewayUrl, [
                'puerto' => config('balanza.puerto', 'COM1')
            ]);

            if ($response->failed()) {
                throw new Exception("Gateway no responde");
            }

            $data = $response->json();

            if (!$data['success']) {
                throw new Exception($data['error'] ?? 'Error desconocido');
            }

            return [
                'peso' => $data['peso'],
                'unidad' => $data['unidad'] ?? 'kg',
                'estatus' => [
                    'equilibrio' => $data['estable'] ?? false
                ]
            ];
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage());
        }
    }
}