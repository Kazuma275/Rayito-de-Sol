<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\FavoriteController;
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

// --- RUTAS PROTEGIDAS ---
Route::middleware('auth:sanctum')->group(function () {
    // Perfil de usuario
    Route::get('/user/profile', [UserController::class, 'profile']);
    Route::patch('/user/profile', [UserController::class, 'updateProfile']);

    // Favoritos del usuario
    Route::get('/user/favorites', [FavoriteController::class, 'index']);

    // Reservas (Bookings) ADMIN (todas)
    Route::get('/bookings', [ReservationController::class, 'index']);

    // Crear reserva (huésped)
    Route::post('/reservations', [ReservationController::class, 'store']);

    // Ver reservas sólo de las propiedades del propietario autenticado
    Route::get('/owner/bookings', [ReservationController::class, 'ownerBookings']);

    // Gestionar reservas por propiedad concreta
    Route::get('/properties/{id}/bookings', [ReservationController::class, 'propertyBookings']);

    Route::get('/user/summary', [UserController::class, 'dashboardSummary']);

    // Acciones sobre reservas específicas (requiere métodos en el controller)
    Route::post('/reservations/{reservation}/accept', [ReservationController::class, 'accept']);
    Route::post('/reservations/{reservation}/reject', [ReservationController::class, 'reject']);

    Route::middleware('auth:sanctum')->get('/statistics', [App\Http\Controllers\StatisticsController::class, 'statistics']);

});

// --- PROPIEDADES ---
Route::get('/properties', [PropertyController::class, 'index']);
Route::get('/properties/{id}', [PropertyController::class, 'show']);
Route::post('/properties', [PropertyController::class, 'store']);

// --- DÍAS NO DISPONIBLES ---
Route::get('/properties/{id}/unavailable-dates', [UnavailableDateController::class, 'index']);
Route::post('/properties/{id}/unavailable-dates', [UnavailableDateController::class, 'store']);
Route::delete('/properties/{id}/unavailable-dates', [UnavailableDateController::class, 'destroy']);

// --- PRECIOS POR DÍA ---
Route::get('/properties/{id}/day-prices', [PropertyDayPriceController::class, 'index']);
Route::post('/properties/{id}/day-prices', [PropertyDayPriceController::class, 'store']);
Route::delete('/properties/{id}/day-prices', [PropertyDayPriceController::class, 'destroy']);

// --- MENSAJES ---
// Route::get('/messages', [MessageController::class, 'index']);
