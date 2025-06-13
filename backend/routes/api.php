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
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SupportController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| AUTENTICACIÓN
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthenticatedSessionController::class, 'register']);
Route::post('/login', [AuthenticatedSessionController::class, 'login'])->name('login');

/*
|--------------------------------------------------------------------------
| PROPIEDADES (PÚBLICO)
|--------------------------------------------------------------------------
| Todas las rutas públicas relacionadas a propiedades
*/
Route::middleware('auth:sanctum')->get('/properties', [PropertyController::class, 'index']);
Route::get('/properties/search', [PropertyController::class, 'search']);
Route::get('/properties/{id}', [PropertyController::class, 'show']);
Route::get('/properties/{id}/unavailable-dates', [UnavailableDateController::class, 'index']);
Route::get('/properties/{id}/day-prices', [PropertyDayPriceController::class, 'index']);

/*
|--------------------------------------------------------------------------
| RESERVAS (PÚBLICO) - PARA PERMITIR VER Y CREAR RESERVAS
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->get('/bookings', [ReservationController::class, 'index']);
Route::middleware('auth:sanctum')->post('/bookings', [ReservationController::class, 'store']);
Route::middleware('auth:sanctum')->get('/bookings/{id}', [ReservationController::class, 'show']);
Route::middleware('auth:sanctum')->put('/bookings/{id}', [ReservationController::class, 'update']);

/*
|--------------------------------------------------------------------------
| RUTAS DE PAGO CON STRIPE (PÚBLICAS)
|--------------------------------------------------------------------------
*/
Route::post('/create-payment-intent', [PaymentController::class, 'createPaymentIntent']);
Route::post('/confirm-payment', [PaymentController::class, 'confirmPayment']);

/*
|--------------------------------------------------------------------------
| MENSAJES Y CONVERSACIONES (PÚBLICO)
|--------------------------------------------------------------------------
| (Agrega aquí las rutas públicas de mensajes/conversaciones si las hay)
*/

/*
|--------------------------------------------------------------------------
| RUTAS PROTEGIDAS POR AUTENTICACIÓN
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    // Propiedades del usuario autenticado
    Route::post('/properties', [PropertyController::class, 'store']);
    Route::put('/properties/{id}', [PropertyController::class, 'update']);
    Route::delete('/properties/{id}', [PropertyController::class, 'destroy']);

    // Bookings & reservas (rutas específicas del propietario)
    Route::get('/owner/bookings', [ReservationController::class, 'ownerBookings']);
    Route::get('/properties/{id}/bookings', [ReservationController::class, 'propertyBookings']);
    Route::post('/bookings/{booking}/accept', [ReservationController::class, 'accept']);
    Route::post('/bookings/{booking}/reject', [ReservationController::class, 'reject']);
    Route::post('/reservations/{reservation}/accept', [ReservationController::class, 'accept']);
    Route::post('/reservations/{reservation}/reject', [ReservationController::class, 'reject']);
    Route::post('/bookings/{id}/cancel', [ReservationController::class, 'cancel']);
    Route::delete('/bookings/{id}', [ReservationController::class, 'destroy']);

    // Rutas específicas de usuario para reservas
    Route::get('/user/{userId}/bookings', [ReservationController::class, 'getByUser']);
    Route::get('/property/{propertyId}/bookings', [ReservationController::class, 'getByProperty']);

    Route::get('/users', [ReservationController::class, 'getUsers']);

    // Días y precios de propiedades
    Route::post('/properties/{id}/day-prices', [PropertyDayPriceController::class, 'store']);
    Route::delete('/properties/{id}/day-prices', [PropertyDayPriceController::class, 'destroy']);
    Route::post('/properties/{id}/unavailable-dates', [UnavailableDateController::class, 'store']);
    Route::delete('/properties/{id}/unavailable-dates', [UnavailableDateController::class, 'destroy']);

    Route::put('properties/{id}', [PropertyController::class, 'update']);

    // Mensajes y conversaciones privadas
    Route::get('/conversations', [ConversationController::class, 'index']);
    Route::post('/conversations', [ConversationController::class, 'store']);
    Route::get('/conversations/{id}', [ConversationController::class, 'show']);
    Route::get('/conversations/{conversation}/messages', [MessageController::class, 'index']);
    Route::post('/conversations/{conversation}/messages', [MessageController::class, 'send']);
    Route::post('/conversations/{conversation}/markAsRead', [MessageController::class, 'markAsRead']);
    Route::post('/conversations/{conversation}/typing', [MessageController::class, 'typing']);

    // Perfil de usuario y favoritos
    Route::get('/user/profile', [UserController::class, 'profile']);
    Route::patch('/user/profile', [UserController::class, 'updateProfile']);
    Route::delete('/user/profile', [UserController::class, 'destroy']);
    Route::post('/user/change-password', [UserController::class, 'changePassword']);
    Route::get('/user/favorites', [FavoriteController::class, 'index']);
    Route::post('/user/favorites', [FavoriteController::class, 'store']);
    Route::delete('/user/favorites/{id}', [FavoriteController::class, 'destroy']);

    // Dashboard y resumen
    Route::get('/user/summary', [UserController::class, 'dashboardSummary']);
    Route::get('/user/renter-summary', [UserController::class, 'renterSummary']);

    // Estadísticas privadas
    Route::get('/statistics', [StatisticsController::class, 'statistics']);

    // Usuario autenticado (info básica)
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

// Rutas para broadcasting con autenticación
Broadcast::routes(['middleware' => ['auth:sanctum']]);

Route::post('/broadcasting/auth', function (Request $request) {
    return Broadcast::auth($request);
})->middleware('auth:sanctum');

/*
|--------------------------------------------------------------------------
| SOPORTE Y UTILIDADES
|--------------------------------------------------------------------------
*/
Route::post('/contact-support', [SupportController::class, 'contact']);

/*
|--------------------------------------------------------------------------
| RECUPERACIÓN DE CONTRASEÑA
|--------------------------------------------------------------------------
*/
Route::post('/forgot-password', [AuthenticatedSessionController::class, 'forgotPassword']);
Route::post('/verify-reset-token', [AuthenticatedSessionController::class, 'verifyResetToken']);
Route::post('/reset-password', [AuthenticatedSessionController::class, 'resetPassword']);

/*
|--------------------------------------------------------------------------
| RUTAS DE DEBUGGING - ELIMINAR EN PRODUCCIÓN
|--------------------------------------------------------------------------
*/
Route::get('/log-test', function () {
    Log::info('¡Laravel está escribiendo en el log!');
    return 'ok';
});
Route::get('/debug-users', function () {
    $users = \App\Models\User::select('id', 'email', 'reset_password_token', 'reset_password_expires_at')->get();
    return response()->json($users);
});
Route::get('/debug-token-search/{token}', function ($token) {
    $user = \App\Models\User::where('reset_password_token', $token)->first();
    return response()->json([
        'token_searched' => $token,
        'user_found' => $user ? true : false,
        'user_data' => $user ? [
            'id' => $user->id,
            'email' => $user->email,
            'reset_password_token' => $user->reset_password_token,
            'reset_password_expires_at' => $user->reset_password_expires_at,
        ] : null,
        'all_users_with_tokens' => \App\Models\User::whereNotNull('reset_password_token')->select('id', 'email', 'reset_password_token')->get()
    ]);
});
