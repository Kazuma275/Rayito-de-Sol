<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
/* use App\Http\Controllers\Auth\PasswordResetLinkController; */
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\UnavailableDateController;
use App\Models\Reservation;
use App\Http\Controllers\PropertyDayPriceController;

Route::post('/register', [AuthenticatedSessionController::class, 'store']);
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
/* Route::post('/forgot-password', [PasswordResetLinkController::class, 'store']); */

Route::get('/test-cors', function () {
    return response()->json(['message' => 'CORS está funcionando!']);
});

// Rutas protegidas por Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/profile', [UserController::class, 'profile']);
    Route::patch('/user/profile', [UserController::class, 'updateProfile']);
    Route::post('/reservations', [ReservationController::class, 'store']);
    // Si quieres proteger más rutas, ponlas aquí.
});

// PROPIEDADES
Route::post('/properties', [PropertyController::class, 'store']);
Route::get('/properties', [PropertyController::class, 'index']);
Route::get('/properties/{id}', [PropertyController::class, 'show']);

// RESERVAS Y BOOKINGS
Route::post('/reservations', [ReservationController::class, 'store']);
Route::get('/bookings', function () {
    return Reservation::all();
});

// FAVORITOS
Route::get('/user/favorites', [FavoriteController::class, 'index']);

// DÍAS NO DISPONIBLES (UnavailableDate)
Route::get('/properties/{id}/unavailable-dates', [UnavailableDateController::class, 'index']);
Route::post('/properties/{id}/unavailable-dates', [UnavailableDateController::class, 'store']);
Route::delete('/properties/{id}/unavailable-dates', [UnavailableDateController::class, 'destroy']);

// Route::get('/messages', [MessageController::class, 'index']);


Route::get('/properties/{id}/day-prices', [PropertyDayPriceController::class, 'index']);
Route::post('/properties/{id}/day-prices', [PropertyDayPriceController::class, 'store']);
Route::delete('/properties/{id}/day-prices', [PropertyDayPriceController::class, 'destroy']);
Route::get('/properties/{id}/bookings', [ReservationController::class, 'propertyBookings']);
