<?php

return [

    /*
    |----------------------------------------------------------------------
    | Laravel CORS Configuration
    |----------------------------------------------------------------------
    |
    | This file is where you can configure your CORS settings for your
    | application. By default, all incoming requests are blocked unless
    | otherwise specified in the 'allowed_origins' array.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],  // Asegurando que CORS sea aplicado a las rutas de la API

    'allowed_methods' => ['*'],  // Permite todos los métodos: GET, POST, PUT, DELETE, etc.

    'allowed_origins' => [
        'http://localhost:5173',  // Aseguramos que tu frontend tenga acceso
    ],

    'allowed_origins_patterns' => [
        // Puedes agregar patrones para otros dominios si es necesario
    ],

    'allowed_headers' => ['*'],  // Permitir todos los headers

    'exposed_headers' => [],  // Dejar en blanco si no necesitas exponer headers específicos

    'max_age' => 0,  // Tiempo de cache de las solicitudes CORS

    'supports_credentials' => true,  // Permitir el envío de cookies o credenciales con las solicitudes
];
