<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PesoController extends Controller
{
    public function index()
    {
        return view('peso');
    }

    public function leer(Request $request)
    {
        $puerto = $request->query('puerto', 'COM1');
        $url = 'https://throat-shop-italiano-divided.trycloudflare.com/peso';

        try {
            // Nota: withoutVerifying desactiva la validación SSL sólo para esta llamada.
            // Para producción, configurá el CA bundle en PHP en lugar de desactivar.
            $response = Http::withoutVerifying()->timeout(8)->get($url, [
                'puerto' => $puerto,
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'ok' => false,
                'error' => 'Error en la solicitud a la balanza',
                'status' => $response->status(),
                'body' => $response->body(),
            ], $response->status());
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'error' => 'Excepción al consultar la balanza',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
