<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';

    protected $fillable = [
        'name',
        'description',
    ];

    // Relación: Un rol tiene muchos usuarios
    public function users()
    {
        // 'role_id' es la FK en la tabla users (deberás modificar la migración de users para soportar esto)
        return $this->hasMany(User::class, 'role_id');
    }
}
