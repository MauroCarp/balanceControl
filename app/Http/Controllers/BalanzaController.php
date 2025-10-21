<?php

namespace App\Http\Controllers;

use App\Services\BalanzaService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class BalanzaController extends Controller
{
    protected $balanzaService;

    public function __construct(BalanzaService $balanzaService)
    {
        $this->balanzaService = $balanzaService;
    }

    /**
     * Lee el peso actual de la balanza
     * 
     * @return JsonResponse
     */
    public function leerPeso(Request $request): JsonResponse
    {
        try {
            // Obtener puerto desde la configuración o request
            $puerto = $request->input('puerto', config('balanza.puerto', 'COM1'));
            $baudRate = $request->input('baud_rate', config('balanza.baud_rate', 9600));

            // Crear instancia del servicio con la configuración
            $balanza = new BalanzaService($puerto, $baudRate);

            // Leer el peso
            $resultado = $balanza->leerPeso();

            return response()->json([
                'success' => true,
                'data' => $resultado,
                'message' => 'Peso leído correctamente'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Error al leer el peso de la balanza'
            ], 500);
        }
    }

    /**
     * Lee el peso y espera a que esté estable
     * 
     * @return JsonResponse
     */
    public function leerPesoEstable(Request $request): JsonResponse
    {
        try {
            $puerto = $request->input('puerto', config('balanza.puerto', 'COM1'));
            $baudRate = $request->input('baud_rate', config('balanza.baud_rate', 9600));
            $maxIntentos = $request->input('max_intentos', 10);

            $balanza = new BalanzaService($puerto, $baudRate);
            $resultado = $balanza->esperarPesoEstable($maxIntentos);

            return response()->json([
                'success' => true,
                'data' => $resultado,
                'message' => 'Peso estable obtenido correctamente'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'No se pudo obtener un peso estable'
            ], 500);
        }
    }

    /**
     * Prueba la conexión con la balanza
     * 
     * @return JsonResponse
     */
    public function probarConexion(Request $request): JsonResponse
    {
        try {
            $puerto = $request->input('puerto', config('balanza.puerto', 'COM1'));
            $baudRate = $request->input('baud_rate', config('balanza.baud_rate', 9600));

            $balanza = new BalanzaService($puerto, $baudRate);
            $resultado = $balanza->leerPeso();

            return response()->json([
                'success' => true,
                'data' => $resultado,
                'message' => 'Conexión exitosa con la balanza',
                'config' => [
                    'puerto' => $puerto,
                    'baud_rate' => $baudRate
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'No se pudo conectar con la balanza',
                'config' => [
                    'puerto' => $request->input('puerto', config('balanza.puerto', 'COM1')),
                    'baud_rate' => $request->input('baud_rate', config('balanza.baud_rate', 9600))
                ]
            ], 500);
        }
    }
}
