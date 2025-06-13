<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Conversación entre dos usuarios sobre una propiedad y/o reserva.
 *
 * @OA\Schema(
 *   schema="Conversation",
 *   type="object",
 *   required={"id", "user_one_id", "user_two_id"},
 *   @OA\Property(property="id", type="integer", example=11, description="ID único de la conversación"),
 *   @OA\Property(property="user_one_id", type="integer", example=5, description="ID del primer participante (user_one)"),
 *   @OA\Property(property="user_two_id", type="integer", example=7, description="ID del segundo participante (user_two)"),
 *   @OA\Property(property="property_id", type="integer", example=3, description="ID de la propiedad asociada"),
 *   @OA\Property(property="reservation_id", type="integer", example=12, description="ID de la reserva asociada"),
 *   @OA\Property(property="guest_id", type="integer", example=7, description="ID del usuario invitado (guest)"),
 *   @OA\Property(property="owner_id", type="integer", example=5, description="ID del propietario (owner)"),
 *   @OA\Property(property="starred_by_guest", type="boolean", example=true, description="¿La conversación está destacada para el huésped?"),
 *   @OA\Property(property="starred_by_owner", type="boolean", example=false, description="¿La conversación está destacada para el propietario?"),
 *   @OA\Property(property="archived_by_guest", type="boolean", example=false, description="¿La conversación está archivada para el huésped?"),
 *   @OA\Property(property="archived_by_owner", type="boolean", example=false, description="¿La conversación está archivada para el propietario?"),
 *   @OA\Property(property="created_at", type="string", format="date-time", example="2025-06-13T10:20:00Z"),
 *   @OA\Property(property="updated_at", type="string", format="date-time", example="2025-06-13T10:20:00Z"),
 *   @OA\Property(
 *     property="messages",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/Message"),
 *     description="Mensajes de la conversación"
 *   ),
 *   @OA\Property(
 *     property="lastMessage",
 *     ref="#/components/schemas/Message",
 *     description="Último mensaje (si se incluye en la respuesta)"
 *   )
 * )
 */
class Conversation extends Model
{
    use HasFactory;

    protected $table = 'conversations';

    protected $fillable = [
        'user_one_id',
        'user_two_id',
        'property_id',
        'reservation_id',
        'guest_id',
        'owner_id',
        'property_id',
        'reservation_id',
        'starred_by_guest',
        'starred_by_owner',
        'archived_by_guest',
        'archived_by_owner',
    ];

    public function guest() { return $this->belongsTo(User::class, 'guest_id'); }
    public function owner() { return $this->belongsTo(User::class, 'owner_id'); }
    public function messages() { return $this->hasMany(Message::class); }
    public function property() { return $this->belongsTo(Property::class); }
    public function reservation() { return $this->belongsTo(Reservation::class); }
    public function userOne() { return $this->belongsTo(User::class, 'user_one_id'); }
    public function userTwo() { return $this->belongsTo(User::class, 'user_two_id'); }
    public function lastMessage() { return $this->hasOne(Message::class)->latestOfMany(); }
}
