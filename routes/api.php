<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rutas para la balanza digital
Route::prefix('balanza')->group(function () {
    Route::get('/leer-peso', [App\Http\Controllers\BalanzaController::class, 'leerPeso']);
});

Route::get('/balanza/leer', function () {
    $url = config('balanza.python_url'); // e.g. https://abcd1234.ngrok-free.app/read
    $resp = Http::timeout(5)->get($url);

    if ($resp->failed()) {
        return response()->json(['error' => 'Fallo al contactar servicio Python'], 502);
    }

    $data = $resp->json();
    // (Opcional) Guardar
    \Illuminate\Support\Facades\Storage::put('balanza/ultima.json', json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
    return response()->json($data);
});