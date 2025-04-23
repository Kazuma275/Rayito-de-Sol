<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel CORS
    |--------------------------------------------------------------------------
    |
    | This file is where you can configure your CORS settings for your
    | application. By default, all incoming requests are blocked unless
    | otherwise specified in the `allowed_origins` array.
    |
    */

    'allowed_origins' => [
        'http://localhost:5173',
    ],

    'allowed_origins_patterns' => [
        // Usar patrones para permitir más de un dominio o subdominio
    ],

    'allowed_headers' => [
        'Content-Type',
        'X-Requested-With',
        'Authorization',
    ],

    'allowed_methods' => [
        'GET',
        'POST',
        'PUT',
        'DELETE',
        'PATCH',
    ],

    'exposed_headers' => [
        // 'X-RateLimit-Limit',
        // 'X-RateLimit-Remaining',
    ],

    'max_age' => 0,

    'supports_credentials' => true,


];
