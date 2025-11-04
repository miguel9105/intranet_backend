<?php // <--- FALTA ESTA ETIQUETA

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\RegionalController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route; // <--- ESENCIAL PARA USAR EL FACADE ROUTE

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aquí es donde puedes registrar rutas de API para tu aplicación.
|
*/

// Rutas de Acceso (NO Requieren Token)
Route::post('/users/login', [UserController::class, 'login']);
Route::post('/users', [UserController::class, 'store']); // Registro

// Rutas Protegidas (Requieren Token Bearer: auth:sanctum)
Route::middleware('auth:sanctum')->group(function () {
    
    // Ruta de Cierre de Sesión
    Route::post('/logout', [UserController::class, 'logout']);
    
    // Rutas de Recursos Protegidos (CRUD)
    Route::apiResource('users', UserController::class)->except(['store']);
    Route::apiResource('companies', CompanyController::class);
    Route::apiResource('regionals', RegionalController::class);
    Route::apiResource('positions', PositionController::class);
    Route::apiResource('roles', RoleController::class);
});
