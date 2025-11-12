<?php

use App\Http\Controllers\Api\MermaHumedadController;
use App\Http\Controllers\BalanzaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/admin');
});

Route::post('/merma-humedad', [MermaHumedadController::class, 'getMermaHumedad']);

/*
|--------------------------------------------------------------------------
| Rutas de Balanza Digital
|--------------------------------------------------------------------------
*/
Route::prefix('balanza')->group(function () {
    Route::get('/', [BalanzaController::class, 'index'])->name('balanza.test');
    Route::post('/request', [BalanzaController::class, 'requestWeight'])->name('balanza.request');
    Route::get('/job-status', [BalanzaController::class, 'jobStatus'])->name('balanza.job_status');
    Route::get('/latest', [BalanzaController::class, 'latest'])->name('balanza.latest');
    Route::get('/diagnostico', [BalanzaController::class, 'diagnostico'])->name('balanza.diagnostico');
});
