<?php

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
// Route::get('/barlovento-ingresos/ingreso-barlovento', function () {
//     return view('filament.resources.barlovento-ingresos-resource.pages.ingreso-barlovento');
// })->name('ingreso-barlovento');