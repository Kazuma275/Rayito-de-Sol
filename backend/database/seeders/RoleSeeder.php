<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        Role::create([
            'name' => 'admin',
            'display_name' => 'Administrador',
            'description' => 'Usuario con permisos de administración'
        ]);

        Role::create([
            'name' => 'user',
            'display_name' => 'Usuario',
            'description' => 'Usuario normal del sistema'
        ]);
    }
}
