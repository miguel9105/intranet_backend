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
    // 👈 ¡CAMBIO CLAVE! Sintaxis de Spatie para múltiples roles usa "|"
    Route::middleware('role:Asesor|Administrativo|Gestor|Administrador')->group(function () {
        Route::apiResource('inventario', InventoryController::class);
    });

    // --- 3. MODULO MESA DE AYUDA ---
    // 👈 ¡CAMBIO CLAVE! Sintaxis de Spatie para múltiples roles usa "|"
    Route::middleware('role:Administrativo|Gestor|Administrador')->group(function () {
        Route::apiResource('mesa_ayuda', HelpTableController::class);
    });

    // --- 4. MODULO DE CARTERA ---
    // 👈 ¡CAMBIO CLAVE! Sintaxis de Spatie para múltiples roles usa "|"
    Route::middleware('role:Gestor|Administrador')->group(function () {
        Route::apiResource('cartera', WalletController::class);
    });
});

// // Rutas Protegidas (Requieren Token Bearer: auth:sanctum)
// Route::middleware('auth:sanctum')->group(function () {
    
//     // Ruta de Cierre de Sesión
//     Route::post('/logout', [UserController::class, 'logout']);
    
//     // El 'Administrador' accede a todas las rutas.

//     // 1. FUNCIONES GENERALES DE GESTIÓN (Administrador)
//     // El administrador accede a todas las funciones CRUD y de gestión de usuarios/roles.
//     Route::middleware('role:Administrador')->group(function () { 
//         Route::apiResource('users', UserController::class)->except(['store']);
//         Route::apiResource('companies', CompanyController::class);
//         Route::apiResource('regionals', RegionalController::class);
//         Route::apiResource('positions', PositionController::class);
//         Route::apiResource('roles', RoleController::class);
        
//         // --- NUEVA RUTA: Procesamiento de archivos DataCredito ---
//         Route::post('/iniciar-datacredito', [ProcesamientoController::class, 'procesar']); 
//         // ---------------------------------------------------------
//     });
//     // 2. MODULO DE INVENTARIO (Asesor, Administrativo, Gestor, Administrador)
//     // Se corrigen los roles con COMAS (,)
//     Route::middleware('role:Asesor:Administrativo:Gestor:Administrador')->group(function () {
//         // Asuma que aquí va su controlador de Inventario
//          Route::apiResource('inventario', InventoryController::class); 
//     });

//     // 3. MODULO MESA DE AYUDA (Administrativo, Gestor, Administrador)
//     // Se corrigen los roles con COMAS (,)
//     Route::middleware('role:Administrativo:Gestor:Administrador')->group(function () {
//         // Asuma que aquí va su controlador de MesaAyuda
//         Route::apiResource('mesa_ayuda', HelpTableController::class);
//     });

//     // 4. MODULO DE CARTERA (Gestor, Administrador)
//     // Se corrigen los roles con COMAS (,)
//     Route::middleware('role:Gestor:Administrador')->group(function () {
//         // Asuma que aquí va su controlador de Cartera
//         Route::apiResource('cartera', WalletController::class);
//     });
 
    
// });