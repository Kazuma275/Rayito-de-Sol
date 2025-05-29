<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
/* use App\Http\Controllers\Auth\PasswordResetLinkController; */
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\ReservationController;

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
    Route::post('/reservations', [ReservationController::class, 'store']);
});

// routes/api.php
Route::post('/properties', [PropertyController::class, 'store']);
Route::get('/properties', [PropertyController::class, 'index']);

Route::post('/reservations', [ReservationController::class, 'store']);
use App\Models\Reservation;
Route::get('/bookings', function () {
    return Reservation::all();
});
