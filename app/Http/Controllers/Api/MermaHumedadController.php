<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MermaHumedadController extends Controller
{
    public function getMermaHumedad(Request $request)
    {
        // Validar los datos recibidos
        $validated = $request->validate([
            'cereal' => 'required|string',
            'humedad' => 'required|numeric',
        ]);

        // Buscar el valor de merma en la base de datos
        $merma = DB::table('merma_humedad')
            ->where('cereal', $validated['cereal'])
            ->where('humedad', $validated['humedad'])
            ->value('merma');

        // Retornar el resultado como JSON
        if ($merma !== null) {
            return response()->json(['merma' => $merma]);
        }

        return response()->json(['error' => 'No se encontró el valor de merma.'], 404);
    }
}

