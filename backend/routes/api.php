<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\UnavailableDateController;
use App\Http\Controllers\PropertyDayPriceController;
use App\Http\Controllers\StatisticsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aquí se registran las rutas de la API.
|
*/

// --- AUTENTICACIÓN ---
Route::post('/register', [AuthenticatedSessionController::class, 'register']);
Route::post('/login', [AuthenticatedSessionController::class, 'login']);
// Route::post('/forgot-password', [PasswordResetLinkController::class, 'store']);

// Prueba CORS (elimina en producción)
Route::get('/test-cors', function () {
    return response()->json(['message' => 'CORS está funcionando!']);
});

// --- PROPIEDADES (PÚBLICO) ---
Route::get('/properties', [PropertyController::class, 'index']);
Route::get('/properties/{id}', [PropertyController::class, 'show']);

// --- DÍAS NO DISPONIBLES (PÚBLICO) ---
Route::get('/properties/{id}/unavailable-dates', [UnavailableDateController::class, 'index']);

// --- PRECIOS POR DÍA (PÚBLICO) ---
Route::get('/properties/{id}/day-prices', [PropertyDayPriceController::class, 'index']);

// Precios por día (crear y borrar)
Route::post('/properties/{id}/day-prices', [PropertyDayPriceController::class, 'store']);
Route::delete('/properties/{id}/day-prices', [PropertyDayPriceController::class, 'destroy']);
Route::post('/properties/{id}/unavailable-dates', [UnavailableDateController::class, 'store']);
Route::delete('/properties/{id}/unavailable-dates', [UnavailableDateController::class, 'destroy']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/messages/send', [MessageController::class, 'send']);
    Route::post('/messages/typing', [MessageController::class, 'typing']);
});

// --- RUTAS PROTEGIDAS SOLO LAS NECESARIAS ---
Route::middleware('auth:sanctum')->group(function () {
    // Perfil de usuario
    Route::get('/user/profile', [UserController::class, 'profile']);
    Route::patch('/user/profile', [UserController::class, 'updateProfile']);
    Route::post('/user/change-password', [UserController::class, 'changePassword']);
    Route::get('/user/favorites', [FavoriteController::class, 'index']);
    Route::get('/user/summary', [UserController::class, 'dashboardSummary']);

    // Reservas
    Route::get('/bookings', [ReservationController::class, 'index']);
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::get('/owner/bookings', [ReservationController::class, 'ownerBookings']);
    Route::get('/properties/{id}/bookings', [ReservationController::class, 'propertyBookings']);
    Route::post('/reservations/{reservation}/accept', [ReservationController::class, 'accept']);
    Route::post('/reservations/{reservation}/reject', [ReservationController::class, 'reject']);

    // Crear propiedad
    Route::post('/properties', [PropertyController::class, 'store']);


    // Mensajes y conversaciones
    Route::get('/conversations', [ConversationController::class, 'index']);
    Route::post('/conversations', [ConversationController::class, 'store']);
    Route::get('/conversations/{id}', [ConversationController::class, 'show']);
    Route::get('/conversations/{conversation}/messages', [MessageController::class, 'index']);
    Route::post('/conversations/{conversation}/messages', [MessageController::class, 'store']);

    // Estadísticas
    Route::get('/statistics', [StatisticsController::class, 'statistics']);
});
