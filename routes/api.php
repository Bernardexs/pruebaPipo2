<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Personas;
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

Route::group(['middleware' => ["auth:sanctum"]], function () {
    Route::get('/auth/candidatos', [AuthController::class, 'listadoCandidatosConDetalles']);
    Route::get('/auth/lista-candidatos-total-votos', [AuthController::class, 'listaCandidatosConTotalVotos']);
    Route::post('/auth/ingresar-voto', [AuthController::class, 'ingresarVoto']);
    Route::put('/auth/candidatos/{id}', [AuthController::class, 'actualizarCandidato']);
    Route::delete('/auth/candidatos/{id}', [AuthController::class, 'eliminarCandidato']);


});

Route::post('/auth/register', [AuthController::class, 'createUser']);
Route::post('/auth/login', [AuthController::class, 'loginUser']);

