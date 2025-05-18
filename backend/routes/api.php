<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
/* use App\Http\Controllers\Auth\PasswordResetLinkController; */
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PropertyController;

// Rutas de la API
Route::post('/register', [AuthenticatedSessionController::class, 'store']);
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
/* Route::post('/forgot-password', [PasswordResetLinkController::class, 'store']); */
Route::get('/test-cors', function () {
    return response()->json(['message' => 'CORS está funcionando!']);
});
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/profile', [UserController::class, 'profile']);
    Route::patch('/user/profile', [UserController::class, 'updateProfile']);
});

// routes/api.php
Route::post('/properties', [PropertyController::class, 'store']);
