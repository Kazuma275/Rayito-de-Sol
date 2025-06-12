<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Events\MessageSent;
use App\Http\Controllers\Admin\AdminSettingsController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/probar-broadcast', function () {
    $message = new \App\Models\Message();
    $conversationId = 1;
    broadcast(new MessageSent($message, $conversationId));
    return 'Evento enviado';
});

// Rutas de autenticación web
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

// Rutas de administración agrupadas

/* Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () */

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function (){
    Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings.index');
    Route::get('/settings/users', [AdminSettingsController::class, 'users'])->name('settings.users');
    Route::put('/settings/users/{user}', [AdminSettingsController::class, 'updateUser'])->name('settings.users.update');
    Route::delete('/settings/users/{user}', [AdminSettingsController::class, 'deleteUser'])->name('settings.users.delete');
    Route::get('/settings/properties', [AdminSettingsController::class, 'properties'])->name('settings.properties');
    Route::get('/settings/reservations', [AdminSettingsController::class, 'reservations'])->name('settings.reservations');
    Route::get('/settings/system', [AdminSettingsController::class, 'systemSettings'])->name('settings.system');
});

// ¡ESTO ES CLAVE PARA QUE /broadcasting/auth FUNCIONE!
Broadcast::routes(['middleware' => ['auth:sanctum']]);

// Ruta de prueba sin middleware
Route::get('/admin-test', function () {
    return 'Ruta de prueba funcionando';
});
