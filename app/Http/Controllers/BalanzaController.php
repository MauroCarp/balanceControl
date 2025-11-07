<?php

namespace App\Http\Controllers;

use App\Services\BalanzaService;
use Illuminate\Http\JsonResponse;

class BalanzaController extends Controller
{
    protected $balanzaService;

    public function __construct(BalanzaService $balanzaService)
    {
        $this->balanzaService = $balanzaService;
    }

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
}
