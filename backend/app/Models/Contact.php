<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de Contacto: mensajes enviados por usuarios o clientes.
 *
 * @OA\Schema(
 *   schema="Contact",
 *   type="object",
 *   required={"id", "user_id", "customer_name", "customer_email", "message", "created_at"},
 *   @OA\Property(property="id", type="integer", example=1, description="ID único del mensaje de contacto"),
 *   @OA\Property(property="user_id", type="integer", example=2, description="ID del usuario asociado (puede ser null si es anónimo)"),
 *   @OA\Property(property="customer_name", type="string", example="Juan Pérez", description="Nombre del cliente o usuario"),
 *   @OA\Property(property="customer_email", type="string", format="email", example="juan@email.com", description="Email del cliente o usuario"),
 *   @OA\Property(property="message", type="string", example="Quería consultar disponibilidad del apartamento.", description="Mensaje enviado"),
 *   @OA\Property(property="created_at", type="string", format="date-time", example="2025-06-13T10:20:00Z", description="Fecha de creación")
 * )
 */
class Contact extends Model
{
    use HasFactory;

    protected $table = 'contact';

    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_email',
        'message',
        'created_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
