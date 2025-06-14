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
|
| Rutas principales de la API para la documentación Swagger/OpenAPI.
| Cada bloque indica una agrupación de endpoints.
| Recuerda que la documentación real de Swagger se encuentra en los controladores.
| Estos comentarios sirven de referencia rápida para la estructura de tu API.
*/

/*
|--------------------------------------------------------------------------
| AUTENTICACIÓN
|--------------------------------------------------------------------------
|
| @route POST /register Registrar nuevo usuario
| @route POST /login Iniciar sesión y obtener token de acceso
*/
Route::post('/register', [AuthenticatedSessionController::class, 'register']);
Route::post('/login', [AuthenticatedSessionController::class, 'login'])->name('login');

/*
|--------------------------------------------------------------------------
| PROPIEDADES (PÚBLICO)
|--------------------------------------------------------------------------
|
| Endpoints públicos para consultar propiedades, buscar, ver detalles, días no disponibles y precios diarios.
| @route GET /properties Listar propiedades (requiere autenticación)
| @route GET /properties/search Buscar propiedades disponibles (pública)
| @route GET /properties/{id} Ver detalles de una propiedad
| @route GET /properties/{id}/unavailable-dates Días no disponibles de la propiedad
| @route GET /properties/{id}/day-prices Precios personalizados por día
*/
Route::middleware('auth:sanctum')->get('/properties', [PropertyController::class, 'index']);
Route::get('/properties/search', [PropertyController::class, 'search']);
Route::get('/properties/{id}', [PropertyController::class, 'show']);
Route::get('/properties/{id}/unavailable-dates', [UnavailableDateController::class, 'index']);
Route::get('/properties/{id}/day-prices', [PropertyDayPriceController::class, 'index']);

/*
|--------------------------------------------------------------------------
| RESERVAS (PÚBLICO)
|--------------------------------------------------------------------------
|
| Endpoints públicos para visualizar y crear reservas.
| @route GET /bookings Listar mis reservas (requiere autenticación)
| @route POST /bookings Crear reserva (requiere autenticación)
| @route GET /bookings/{id} Ver detalles de mi reserva
| @route PUT /bookings/{id} Actualizar mi reserva
*/
Route::middleware('auth:sanctum')->get('/bookings', [ReservationController::class, 'index']);
Route::middleware('auth:sanctum')->post('/bookings', [ReservationController::class, 'store']);
Route::middleware('auth:sanctum')->get('/bookings/{id}', [ReservationController::class, 'show']);
Route::middleware('auth:sanctum')->put('/bookings/{id}', [ReservationController::class, 'update']);

/*
|--------------------------------------------------------------------------
| RUTAS DE PAGO CON STRIPE (PÚBLICAS)
|--------------------------------------------------------------------------
|
| Endpoints para gestión de pagos con Stripe.
| @route POST /create-payment-intent Crear intención de pago
| @route POST /confirm-payment Confirmar estado del pago
*/
Route::post('/create-payment-intent', [PaymentController::class, 'createPaymentIntent']);
Route::post('/confirm-payment', [PaymentController::class, 'confirmPayment']);

/*
|--------------------------------------------------------------------------
| MENSAJES Y CONVERSACIONES (PÚBLICO)
|--------------------------------------------------------------------------
| (Endpoints públicos de mensajes/conversaciones. Agrega aquí si los hay.)
*/

/*
|--------------------------------------------------------------------------
| RUTAS PROTEGIDAS POR AUTENTICACIÓN
|--------------------------------------------------------------------------
|
| Todas las rutas de este grupo requieren autenticación con Sanctum (token Bearer).
*/
Route::middleware('auth:sanctum')->group(function () {
    // Propiedades del usuario autenticado
    // @route POST /properties Crear propiedad
    // @route PUT /properties/{id} Actualizar propiedad
    // @route DELETE /properties/{id} Eliminar propiedad
    Route::post('/properties', [PropertyController::class, 'store']);
    Route::put('/properties/{id}', [PropertyController::class, 'update']);
    Route::delete('/properties/{id}', [PropertyController::class, 'destroy']);

    // Bookings & reservas (solo propietario)
    // @route GET /owner/bookings Reservas de todas mis propiedades
    // @route GET /properties/{id}/bookings Reservas de una propiedad
    // @route POST /bookings/{booking}/accept Aceptar reserva
    // @route POST /bookings/{booking}/reject Rechazar reserva
    // @route POST /reservations/{reservation}/accept Aceptar reserva (alias)
    // @route POST /reservations/{reservation}/reject Rechazar reserva (alias)
    // @route POST /bookings/{id}/cancel Cancelar reserva
    // @route DELETE /bookings/{id} Eliminar reserva
    Route::get('/owner/bookings', [ReservationController::class, 'ownerBookings']);
    Route::get('/properties/{id}/bookings', [ReservationController::class, 'propertyBookings']);
    Route::post('/bookings/{booking}/accept', [ReservationController::class, 'accept']);
    Route::post('/bookings/{booking}/reject', [ReservationController::class, 'reject']);
    Route::post('/reservations/{reservation}/accept', [ReservationController::class, 'accept']);
    Route::post('/reservations/{reservation}/reject', [ReservationController::class, 'reject']);
    Route::post('/bookings/{id}/cancel', [ReservationController::class, 'cancel']);
    Route::delete('/bookings/{id}', [ReservationController::class, 'destroy']);

    // Rutas de reservas por usuario y por propiedad
    // @route GET /user/{userId}/bookings Reservas por usuario
    // @route GET /property/{propertyId}/bookings Reservas por propiedad
    Route::get('/user/{userId}/bookings', [ReservationController::class, 'getByUser']);
    Route::get('/property/{propertyId}/bookings', [ReservationController::class, 'getByProperty']);

    // Usuarios (listado)
    // @route GET /users Listar usuarios con reservas
    Route::get('/users', [ReservationController::class, 'getUsers']);

    // Días y precios de propiedades
    // @route POST /properties/{id}/day-prices Guardar precio de un día
    // @route DELETE /properties/{id}/day-prices Eliminar precio personalizado de un día
    // @route POST /properties/{id}/unavailable-dates Agregar día no disponible
    // @route DELETE /properties/{id}/unavailable-dates Quitar día no disponible
    Route::post('/properties/{id}/day-prices', [PropertyDayPriceController::class, 'store']);
    Route::delete('/properties/{id}/day-prices', [PropertyDayPriceController::class, 'destroy']);
    Route::post('/properties/{id}/unavailable-dates', [UnavailableDateController::class, 'store']);
    Route::delete('/properties/{id}/unavailable-dates', [UnavailableDateController::class, 'destroy']);

    // Mensajes y conversaciones privadas
    // @route GET /conversations Listar conversaciones
    // @route POST /conversations Crear conversación
    // @route GET /conversations/{id} Ver conversación
    // @route GET /conversations/{conversation}/messages Listar mensajes
    // @route POST /conversations/{conversation}/messages Enviar mensaje
    // @route POST /conversations/{conversation}/markAsRead Marcar como leídos
    // @route POST /conversations/{conversation}/typing Escribir (evento typing)
    Route::get('/conversations', [ConversationController::class, 'index']);
    Route::post('/conversations', [ConversationController::class, 'store']);
    Route::get('/conversations/{id}', [ConversationController::class, 'show']);
    Route::get('/conversations/{conversation}/messages', [MessageController::class, 'index']);
    Route::post('/conversations/{conversation}/messages', [MessageController::class, 'send']);
    Route::post('/conversations/{conversation}/markAsRead', [MessageController::class, 'markAsRead']);
    Route::post('/conversations/{conversation}/typing', [MessageController::class, 'typing']);

    // Perfil de usuario y favoritos
    // @route GET /user/profile Ver perfil
    // @route PATCH /user/profile Actualizar perfil
    // @route DELETE /user/profile Eliminar perfil
    // @route POST /user/change-password Cambiar contraseña
    // @route GET /user/favorites Listar favoritos
    // @route POST /user/favorites Añadir favorito
    // @route DELETE /user/favorites/{id} Quitar favorito
    Route::get('/user/profile', [UserController::class, 'profile']);
    Route::patch('/user/profile', [UserController::class, 'updateProfile']);
    Route::delete('/user/profile', [UserController::class, 'destroy']);
    Route::post('/user/change-password', [UserController::class, 'changePassword']);
    Route::get('/user/favorites', [FavoriteController::class, 'index']);
    Route::post('/user/favorites', [FavoriteController::class, 'store']);
    Route::delete('/user/favorites/{id}', [FavoriteController::class, 'destroy']);

    // Dashboard y resumen
    // @route GET /user/summary Resumen para dashboard
    // @route GET /user/renter-summary Resumen como inquilino
    Route::get('/user/summary', [UserController::class, 'dashboardSummary']);
    Route::get('/user/renter-summary', [UserController::class, 'renterSummary']);

    // Estadísticas privadas
    // @route GET /statistics Estadísticas del propietario
    Route::get('/statistics', [StatisticsController::class, 'statistics']);

    // Usuario autenticado (info básica)
    // @route GET /user Info de usuario autenticado
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

// Rutas para broadcasting con autenticación
// @route GET /broadcasting/auth Autenticación de broadcasting para websockets
Broadcast::routes(['middleware' => ['auth:sanctum']]);
Route::post('/broadcasting/auth', function (Request $request) {
    return Broadcast::auth($request);
})->middleware('auth:sanctum');

/*
|--------------------------------------------------------------------------
| SOPORTE Y UTILIDADES
|--------------------------------------------------------------------------
|
| @route POST /contact-support Contactar soporte técnico
*/
Route::post('/contact-support', [SupportController::class, 'contact']);

/*
|--------------------------------------------------------------------------
| RECUPERACIÓN DE CONTRASEÑA
|--------------------------------------------------------------------------
|
| Endpoints para restaurar acceso mediante token de recuperación.
| @route POST /forgot-password Solicitar recuperación de contraseña
| @route POST /verify-reset-token Verificar token de recuperación
| @route POST /reset-password Restablecer contraseña
*/
Route::post('/forgot-password', [AuthenticatedSessionController::class, 'forgotPassword']);
Route::post('/verify-reset-token', [AuthenticatedSessionController::class, 'verifyResetToken']);
Route::post('/reset-password', [AuthenticatedSessionController::class, 'resetPassword']);

/*
|--------------------------------------------------------------------------
| RUTAS DE DEBUGGING - ELIMINAR EN PRODUCCIÓN
|--------------------------------------------------------------------------
|
| Endpoints solo para pruebas y depuración. ¡No exponer en producción!
| @route GET /log-test Escribir en el log
| @route GET /debug-users Ver usuarios y tokens de recuperación
| @route GET /debug-token-search/{token} Buscar usuario por token de recuperación
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
