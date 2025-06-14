<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Mensaje individual dentro de una conversación.
 *
 * @OA\Schema(
 *   schema="Message",
 *   type="object",
 *   required={"id", "conversation_id", "sender_id", "text", "created_at"},
 *   @OA\Property(property="id", type="integer", example=44, description="ID único del mensaje"),
 *   @OA\Property(property="conversation_id", type="integer", example=11, description="ID de la conversación a la que pertenece el mensaje"),
 *   @OA\Property(property="sender_id", type="integer", example=5, description="ID del usuario que envía el mensaje"),
 *   @OA\Property(property="text", type="string", example="¿Está disponible el piso el próximo fin de semana?", description="Texto descifrado del mensaje"),
 *   @OA\Property(property="attachment", type="string", nullable=true, example="uploads/adjunto.pdf", description="Ruta al archivo adjunto (si existe)"),
 *   @OA\Property(property="status", type="string", example="sent", description="Estado del mensaje (sent, delivered, read, etc.)"),
 *   @OA\Property(property="read_at", type="string", format="date-time", nullable=true, example="2025-06-13T11:00:00Z", description="Fecha/hora de lectura"),
 *   @OA\Property(property="created_at", type="string", format="date-time", example="2025-06-13T10:20:00Z"),
 *   @OA\Property(property="updated_at", type="string", format="date-time", example="2025-06-13T10:20:00Z")
 * )
 */
class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'text',
        'attachment',
        'status',
        'read_at',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'read_at',
    ];


    // Mutator para cifrar
    public function setTextAttribute($value)
    {
        $this->attributes['text'] = encrypt($value);
    }

    // Accessor para descifrar
    public function getTextAttribute($value)
    {
        if (!$value) return null;
        try {
            return decrypt($value);
        } catch (\Exception $e) {
            return $value; // Por si hay mensajes viejos sin cifrar
        }
    }
}
