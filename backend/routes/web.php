<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Events\MessageSent;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\DebugController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Rutas principales para la interfaz web y panel de administración.
| Estas rutas se usan para vistas, login web y administración.
| No se documentan en Swagger/OpenAPI (que es solo para la API), pero puedes comentar para referencia de estructura.
*/

Route::get('/', function () {
    // Página inicial pública
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Prueba de broadcasting
|--------------------------------------------------------------------------
|
| Ruta de ejemplo para emitir un evento broadcast por websockets.
| @route GET /probar-broadcast
*/
Route::get('/probar-broadcast', function () {
    $message = new \App\Models\Message();
    $conversationId = 1;
    broadcast(new MessageSent($message, $conversationId));
    return 'Evento enviado';
});

/*
|--------------------------------------------------------------------------
| Autenticación web
|--------------------------------------------------------------------------
|
| Pantalla de login, login post y logout para usuarios web (no API).
| @route GET /login Formulario de login
| @route POST /login Procesa login y redirige a /admin/settings
| @route POST /logout Cierra sesión y redirige a login
*/
Route::get('/login', function () {
    if (Auth::check()) {
        return redirect('/admin/settings');
    }
    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('/admin/settings');
    }

    return back()->withErrors([
        'email' => 'Las credenciales no coinciden.',
    ])->withInput();
})->name('login.post');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

/*
|--------------------------------------------------------------------------
| Área de administración (requiere login)
|--------------------------------------------------------------------------
|
| Rutas agrupadas bajo /admin para gestión interna.
| @route GET /admin/settings Panel de configuración
| @route GET /admin/settings/users Gestión de usuarios
| @route PUT /admin/settings/users/{user} Actualizar usuario
| @route DELETE /admin/settings/users/{user} Eliminar usuario
| @route GET /admin/settings/properties Gestión de propiedades
| @route GET /admin/settings/reservations Gestión de reservas
| @route GET /admin/settings/system Ajustes del sistema
*/
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function (){
    Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings.index');
    Route::get('/settings/users', [AdminSettingsController::class, 'users'])->name('settings.users');
    Route::put('/settings/users/{user}', [AdminSettingsController::class, 'updateUser'])->name('settings.users.update');
    Route::delete('/settings/users/{user}', [AdminSettingsController::class, 'deleteUser'])->name('settings.users.delete');
    Route::get('/settings/properties', [AdminSettingsController::class, 'properties'])->name('settings.properties');
    Route::get('/settings/reservations', [AdminSettingsController::class, 'reservations'])->name('settings.reservations');
    Route::get('/settings/system', [AdminSettingsController::class, 'systemSettings'])->name('settings.system');
});

/*
|--------------------------------------------------------------------------
| Broadcasting (websockets/autenticación)
|--------------------------------------------------------------------------
|
| Es necesario para que /broadcasting/auth funcione correctamente en Laravel Echo.
| @route GET /broadcasting/auth
*/
Broadcast::routes(['middleware' => ['auth:sanctum']]);

/*
|--------------------------------------------------------------------------
| Ruta de prueba sin middleware
|--------------------------------------------------------------------------
|
| @route GET /admin-test Ruta de test rápida
*/
Route::get('/admin-test', function () {
    return 'Ruta de prueba funcionando';
});

Route::middleware(['auth', 'verified', 'local'])->group(function () {
    Route::get('/log-test', [DebugController::class, 'logTest'])->name('debug.log-test');
    Route::get('/debug-users', [DebugController::class, 'debugUsers'])->name('debug.debug-users');
    Route::get('/debug-token-search/{token}', [DebugController::class, 'debugTokenSearch'])->name('debug.debug-token-search');
});
