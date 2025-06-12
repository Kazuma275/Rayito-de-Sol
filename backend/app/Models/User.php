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
        'phone',
        'password',
        'token_verificacion',
        'verificado',
        'fecha_registro',
        'role',
        'email_verified_at',
        'remember_token',
        'reset_password_token',
        'reset_password_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'reset_password_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'verificado' => 'boolean',
        'reset_password_expires_at' => 'datetime',
        'fecha_registro' => 'datetime',
    ];

    // Método para verificar si el usuario tiene un rol específico
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    // Método para verificar si el usuario es administrador
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    // Tus relaciones existentes...
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

    // Relación con conversaciones
    public function conversationsAsOne()
    {
        return $this->hasMany(Conversation::class, 'user_one_id');
    }

    public function conversationsAsTwo()
    {
        return $this->hasMany(Conversation::class, 'user_two_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function propertyReservations()
    {
        return $this->hasManyThrough(
            \App\Models\Reservation::class,
            \App\Models\Property::class,
            'user_id',
            'property_id',
            'id',
            'id'
        );
    }
}
