<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $table = 'users';  // Asegúrate de que el nombre de la tabla esté correcto.

    protected $fillable = [
        'username',
        'password',
        'token_verificacion',
        'verificado',
        'fecha_registro',
        'role',
    ];

    // Si usas timestamps, puedes configurarlos así
    public $timestamps = true;
}
