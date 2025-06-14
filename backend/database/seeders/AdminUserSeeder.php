<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Crear usuario administrador
        User::create([
            'name' => 'Administrador',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'verificado' => true,
            'role' => 'admin',
            'fecha_registro' => now(),
        ]);

        // Crear usuario normal
        User::create([
            'name' => 'Usuario Normal',
            'username' => 'user',
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'verificado' => true,
            'role' => 'user',
            'fecha_registro' => now(),
        ]);
    }
}
