<?php 

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\HelpTableController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\RegionalController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletController;
use App\Models\Inventory;
use Illuminate\Support\Facades\Route; 

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Rutas de Acceso (NO Requieren Token)
Route::post('/users/login', [UserController::class, 'login']);
Route::post('/users', [UserController::class, 'store']); // Registro

// Rutas Protegidas (Requieren Token Bearer: auth:sanctum)
Route::middleware('auth:sanctum')->group(function () {
    
    // Ruta de Cierre de Sesión
    Route::post('/logout', [UserController::class, 'logout']);
    
    // El 'Administrador' accede a todas las rutas.
    // No necesita un middleware específico si se le da acceso a las rutas 
    // de todos los demás roles o si su middleware se aplica al grupo más amplio.

    // 1. FUNCIONES GENERALES DE GESTIÓN (Administrador)
    // El administrador accede a todas las funciones CRUD y de gestión de usuarios/roles.
    Route::middleware('role:Administrador')->group(function () { 
        Route::apiResource('users', UserController::class)->except(['store']);
        Route::apiResource('companies', CompanyController::class);
        Route::apiResource('regionals', RegionalController::class);
        Route::apiResource('positions', PositionController::class);
        Route::apiResource('roles', RoleController::class);
    });
    // 2. MODULO DE INVENTARIO (Asesor, Administrativo, Gestor, Administrador)
    // Se corrigen los roles con COMAS (,)
    Route::middleware('role:Asesor,Administrativo,Gestor,Administrador')->group(function () {
        // Asuma que aquí va su controlador de Inventario
         Route::apiResource('inventario', InventoryController::class); 
    });

    // 3. MODULO MESA DE AYUDA (Administrativo, Gestor, Administrador)
    // Se corrigen los roles con COMAS (,)
    Route::middleware('role:Administrativo,Gestor,Administrador')->group(function () {
        // Asuma que aquí va su controlador de MesaAyuda
        Route::apiResource('mesa_ayuda', HelpTableController::class);
    });

    // 4. MODULO DE CARTERA (Gestor, Administrador)
    // Se corrigen los roles con COMAS (,)
    Route::middleware('role:Gestor,Administrador')->group(function () {
        // Asuma que aquí va su controlador de Cartera
        Route::apiResource('cartera', WalletController::class);
    });
 
    
});