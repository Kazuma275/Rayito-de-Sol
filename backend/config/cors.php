<?php

return [

    'paths' => ['api/*', 'broadcasting/auth', 'sanctum/csrf-cookie'],  // Asegura que CORS se aplica a todas las rutas de API

    'allowed_methods' => ['*'],  // Permite todos los métodos: GET, POST, PUT, DELETE, etc.

    'allowed_origins' => [
        'http://localhost:5173',  // Permite el acceso desde tu frontend
    ],

    'allowed_headers' => ['*'],  // Permite todos los encabezados

    'exposed_headers' => [],  // No es necesario exponer encabezados específicos

    'max_age' => 0,  // Tiempo de cache de las solicitudes CORS

    'supports_credentials' => true,  // Permite el envío de cookies y credenciales con las solicitudes
];
