<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TaskController;

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

// Teste de timezone
Route::get('/test/timezone', function () {
    return response()->json([
        'success' => true,
        'data' => [
            'config_timezone' => config('app.timezone'),
            'php_timezone' => date_default_timezone_get(),
            'current_time' => now(),
            'current_time_formatted' => now()->format('d/m/Y H:i:s'),
            'current_time_iso' => now()->toISOString(),
            'carbon_locale' => \Carbon\Carbon::getLocale(),
        ],
        'message' => 'Teste de configuração de timezone'
    ]);
});

// Rotas para Categories
Route::apiResource('categories', CategoryController::class);

// Rotas para Tasks
Route::apiResource('tasks', TaskController::class);

// Rota adicional para atualizar status da tarefa
Route::patch('tasks/{id}/status', [TaskController::class, 'updateStatus']);
