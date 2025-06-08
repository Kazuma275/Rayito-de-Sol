<?php

use Illuminate\Support\Facades\Route;
use App\Events\MensajeEnviado;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/probar-broadcast', function () {
    broadcast(new MensajeEnviado('¡Hola desde Laravel y Pusher!'));
    return 'Evento enviado';
});
