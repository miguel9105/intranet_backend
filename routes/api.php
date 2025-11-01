<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\RegionalController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Rutas de Autenticación (No requieren Token)
Route::post('/login', [UserController::class, 'login'])->name('login');

// Rutas Protegidas (Requieren Token Bearer)
Route::middleware('auth:sanctum')->group(function () {
    
    // Ruta de Cierre de Sesión (Logout)
    Route::post('/logout', [UserController::class, 'logout']);
    
    // Tus otras rutas CRUD (index, show, update, destroy) también deben ir aquí
    Route::apiResource('users', UserController::class)->except(['store']);
});

Route::post('/users', [UserController::class, 'store']);
Route::apiResource('companies', CompanyController::class);
Route::apiResource('regionals', RegionalController::class);
Route::apiResource('positions', PositionController::class);
Route::apiResource('roles', RoleController::class);


