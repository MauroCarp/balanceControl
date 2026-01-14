<?php

use App\Http\Controllers\Api\MermaHumedadController;
use App\Http\Controllers\BalanzaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PesoController;

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
Route::get('/peso', [PesoController::class, 'index'])->name('peso.index');
Route::get('/peso/leer', [PesoController::class, 'leer'])->name('peso.leer');

