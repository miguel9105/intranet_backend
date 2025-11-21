<?php

use App\Http\Controllers\CompanyController;
// Agrega el nuevo controlador
use App\Http\Controllers\DataCreditoController;
use App\Http\Controllers\HelpTableController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProcesamientoController;
use App\Http\Controllers\RegionalController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\ProcesamientoDatacreditoController;
use App\Models\Inventory;
use Illuminate\Support\Facades\Route;


//Ruta de las API
// Rutas de Acceso (NO Requieren Token)
Route::post('/users/login', [UserController::class, 'login']);
Route::post('/users', [UserController::class, 'store']); // Registro

Route::middleware('auth:api')->group(function () {

    // --- Rutas de Autenticación JWT ---
    Route::post('/logout', [UserController::class, 'logout']);
    Route::post('/refresh', [UserController::class, 'refresh']);
    Route::get('/me', [UserController::class, 'me']); // Devuelve el usuario actual

    // --- 1. FUNCIONES GENERALES DE GESTIÓN (Administrador) ---
    // Protegido por el ROL de Spatie
    Route::middleware('role:Administrador')->group(function () {
        // El CRUD de Usuarios (excepto 'store', que es público para registro)
        Route::apiResource('users', UserController::class)->except(['store']);

        Route::apiResource('companies', CompanyController::class);
        Route::apiResource('regionals', RegionalController::class);
        Route::apiResource('positions', PositionController::class);
        // Agrgegar rutas para asignar y quitar roles a usuarios
        // Rutas para Roles y Permisos
        Route::apiResource('roles', RoleController::class);
        Route::apiResource('permissions', PermissionController::class);
        // Asignar un rol a un usuario
        Route::post('/users/{user}/roles', [UserRoleController::class, 'assignRole']);
        // Quitar un rol a un usuario
        Route::delete('/users/{user}/roles/{role}', [UserRoleController::class, 'removeRole']);
        // --- NUEVAS RUTAS: Procesamiento de archivos DataCredito ---
        Route::post('/procesamiento/generar-urls', [ProcesamientoDatacreditoController::class, 'generarUrls']);
        Route::post('/procesamiento/iniciar', [ProcesamientoDatacreditoController::class, 'iniciarProceso']);
        Route::get('/procesamiento/estado', [ProcesamientoDatacreditoController::class, 'verificarEstado']);
    });

    // --- 2. MODULO DE INVENTARIO ---
    Route::middleware('role:Asesor|Administrativo|Gestor|Administrador')->group(function () {
        Route::apiResource('inventario', InventoryController::class);
    });

    // --- 3. MODULO MESA DE AYUDA ---
    Route::middleware('role:Administrativo|Gestor|Administrador')->group(function () {
        Route::apiResource('mesa_ayuda', HelpTableController::class);
    });

    // --- 4. MODULO DE CARTERA ---
    Route::middleware('role:Gestor|Administrador')->group(function () {
        Route::apiResource('cartera', WalletController::class);
    });
});