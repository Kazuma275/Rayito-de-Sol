<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="User",
 *     description="Modelo que representa un usuario del sistema",
 *     required={"id", "name", "email"},
 *     @OA\Property(property="id", type="integer", readOnly=true, example=1),
 *     @OA\Property(property="name", type="string", example="Kazuma"),
 *     @OA\Property(property="username", type="string", example="kazuma123"),
 *     @OA\Property(property="email", type="string", format="email", example="kazuma@example.com"),
 *     @OA\Property(property="phone", type="string", example="+34123456789"),
 *     @OA\Property(property="role", type="string", example="user"),
 *     @OA\Property(property="verificado", type="boolean", example=true),
 *     @OA\Property(property="fecha_registro", type="string", format="date-time", example="2025-01-01T12:00:00Z"),
 *     @OA\Property(property="email_verified_at", type="string", format="date-time", nullable=true, example="2025-01-02T15:00:00Z"),
 *     @OA\Property(property="created_at", type="string", format="date-time", readOnly=true, example="2025-01-01T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", readOnly=true, example="2025-01-10T12:00:00Z")
 * )
 *
 * @OA\Get(
 *     path="/api/users/{id}",
 *     summary="Obtener usuario por ID",
 *     tags={"Usuarios"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID del usuario",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Usuario encontrado",
 *         @OA\JsonContent(ref="#/components/schemas/User")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Usuario no encontrado"
 *     )
 * )
 */
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

    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

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

    public function properties()
    {
        return $this->hasMany(Property::class, 'user_id');
    }

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
