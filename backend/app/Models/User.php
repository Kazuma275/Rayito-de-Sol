<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'token_verificacion',
        'verificado',
        'fecha_registro',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'verificado' => 'boolean',
        'fecha_registro' => 'datetime',
    ];

    public function contacts()
    {
        return $this->hasMany(Contact::class, 'user_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'user_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'user_id');
    }

    public function services()
    {
        return $this->hasMany(Service::class, 'user_id');
    }

    // NUEVA RELACIÓN PARA DASHBOARD
    public function properties()
    {
        return $this->hasMany(Property::class, 'user_id');
    }
}
