<?php

namespace App\Http\Controllers;

use App\Services\BalanzaService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BalanzaController extends Controller
{
    protected $balanzaService;
    protected $gatewayUrl;

    public function __construct(BalanzaService $balanzaService)
    {
        $this->balanzaService = $balanzaService;
        $this->gatewayUrl = config('balanza.gateway_url', url('gateway-balanza.php'));
    }

    /**
     * Método original de lectura directa (mantener compatibilidad)
     */
    public function leerPeso(): JsonResponse
    {
        try {
            $datos = $this->balanzaService->leerPeso();
            
            return response()->json([
                'success' => true,
                'peso' => $datos['peso'],
                'unidad' => $datos['unidad'],
                'estable' => $datos['estatus']['equilibrio']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra la vista de prueba de la balanza
     */
    public function index()
    {
        return view('balanza.test');
    }

    /**
     * Crea una solicitud de lectura de balanza (job)
     * 
     * POST /balanza/request
     * Body: { puerto: "COM1", baudrate: 1200 }
     */
    public function requestWeight(Request $request)
    {
        try {
            $puerto = $request->input('puerto', 'COM1');
            $baudrate = $request->input('baudrate', 1200);

            Log::info('Solicitando lectura de balanza', [
                'puerto' => $puerto,
                'baudrate' => $baudrate
            ]);

            $response = Http::timeout(10)->get($this->gatewayUrl, [
                'action' => 'request',
                'puerto' => $puerto,
                'baudrate' => $baudrate
            ]);

            if ($response->failed()) {
                Log::error('Error al solicitar lectura', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                return response()->json([
                    'success' => false,
                    'error' => 'Error al comunicarse con el gateway'
                ], 500);
            }

            $data = $response->json();
            
            Log::info('Job creado', ['job_id' => $data['job']['id'] ?? null]);

            return response()->json($data);

        } catch (\Exception $e) {
            Log::error('Excepción al solicitar lectura', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Consulta el estado de un job
     * 
     * GET /balanza/job-status?job_id=xxx
     */
    public function jobStatus(Request $request)
    {
        try {
            $jobId = $request->query('job_id');

            if (empty($jobId)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Falta job_id'
                ], 400);
            }

            $response = Http::timeout(5)->get($this->gatewayUrl, [
                'action' => 'job_status',
                'job_id' => $jobId
            ]);

            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Error al consultar estado del job'
                ], 500);
            }

            return response()->json($response->json());

        } catch (\Exception $e) {
            Log::error('Error consultando job status', [
                'job_id' => $request->query('job_id'),
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene la última lectura guardada
     * 
     * GET /balanza/latest
     */
    public function latest()
    {
        try {
            $response = Http::timeout(5)->get($this->gatewayUrl, [
                'action' => 'latest'
            ]);

            if ($response->status() === 404) {
                return response()->json([
                    'success' => false,
                    'error' => 'No hay lecturas disponibles'
                ], 404);
            }

            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Error al obtener última lectura'
                ], 500);
            }

            return response()->json($response->json());

        } catch (\Exception $e) {
            Log::error('Error obteniendo última lectura', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Consulta el estado del gateway (diagnóstico)
     * 
     * GET /balanza/diagnostico
     */
    public function diagnostico()
    {
        try {
            $response = Http::timeout(10)->get($this->gatewayUrl, [
                'action' => 'diagnostico'
            ]);

            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Error al obtener diagnóstico'
                ], 500);
            }

            return response()->json($response->json());

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
