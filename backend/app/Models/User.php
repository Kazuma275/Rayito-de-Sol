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
        'email_verified_at',
        'remember_token',
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
