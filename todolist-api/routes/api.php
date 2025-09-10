<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\AuthController;

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

// Rota de teste simples
Route::get('/test-simple', function () {
    return response()->json(['message' => 'API funcionando!']);
});

// Rota de teste JWT
Route::get('/test-jwt', function () {
    try {
        $user = \App\Models\User::first();
        if (!$user) {
            return response()->json(['message' => 'Nenhum usuário encontrado']);
        }
        
        $token = \Tymon\JWTAuth\Facades\JWTAuth::fromUser($user);
        return response()->json(['message' => 'JWT funcionando!', 'token' => $token]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// Rotas de autenticação (não protegidas)
Route::group(['prefix' => 'auth'], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

// Rotas protegidas por autenticação JWT
Route::group(['middleware' => 'auth:api'], function () {
    // Rotas de autenticação (protegidas)
    Route::group(['prefix' => 'auth'], function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
    });

    // Informações do usuário autenticado
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Rotas para Categories
    Route::apiResource('categories', CategoryController::class);

    // Rotas para Tasks
    Route::apiResource('tasks', TaskController::class);

    // Rota adicional para atualizar status da tarefa
    Route::patch('tasks/{id}/status', [TaskController::class, 'updateStatus']);
});

// Teste de timezone (rota pública para testes)
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
