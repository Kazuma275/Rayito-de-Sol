<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Reservation;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/conversations",
     *     summary="Obtener todas las conversaciones del usuario autenticado (como huésped o propietario)",
     *     tags={"Conversaciones"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Listado de conversaciones",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="propertyId", type="integer", example=5),
     *                 @OA\Property(property="name", type="string", example="Juan Pérez"),
     *                 @OA\Property(property="avatar", type="string", example="https://example.com/avatar.jpg"),
     *                 @OA\Property(property="email", type="string", example="juan@example.com"),
     *                 @OA\Property(property="phone", type="string", example="+34123456789"),
     *                 @OA\Property(
     *                     property="lastMessage",
     *                     type="object",
     *                     @OA\Property(property="text", type="string", example="Hola, ¿sigue disponible?"),
     *                     @OA\Property(property="timestamp", type="string", example="2024-07-01 13:00:00"),
     *                     @OA\Property(property="isOwner", type="boolean", example=true),
     *                     @OA\Property(property="status", type="string", example="read")
     *                 ),
     *                 @OA\Property(property="unread", type="boolean", example=false),
     *                 @OA\Property(
     *                     property="property",
     *                     type="object",
     *                     @OA\Property(property="name", type="string", example="Apartamento Centro"),
     *                     @OA\Property(property="address", type="string", example="Calle Mayor 1"),
     *                     @OA\Property(property="city", type="string", example="Madrid")
     *                 ),
     *                 @OA\Property(
     *                     property="reservation",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=10),
     *                     @OA\Property(property="reservation_date", type="string", example="2024-07-01"),
     *                     @OA\Property(property="checkin", type="string", example="2024-07-10"),
     *                     @OA\Property(property="checkout", type="string", example="2024-07-15"),
     *                     @OA\Property(property="guests", type="integer", example=2),
     *                     @OA\Property(property="paymentStatus", type="string", example="paid")
     *                 ),
     *                 @OA\Property(property="owner_id", type="integer", example=2),
     *                 @OA\Property(property="guest_id", type="integer", example=3),
     *                 @OA\Property(property="user_one_id", type="integer", example=3),
     *                 @OA\Property(property="user_two_id", type="integer", example=2)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $userId = Auth::id();

        $conversations = Conversation::with([
            'property',
            'guest',
            'owner',
            'reservation',
            'lastMessage'
        ])
            ->where('guest_id', $userId)
            ->orWhere('owner_id', $userId)
            ->orderByDesc('updated_at')
            ->get();

        $mapped = $conversations->map(function ($conv) use ($userId) {
            $isOwner = $conv->owner_id === $userId;
            $otherUser = $isOwner ? $conv->guest : $conv->owner;
            $reservation = $conv->reservation;

            return [
                'id' => $conv->id,
                'propertyId' => $conv->property_id,
                'name' => $otherUser->name ?? '',
                'avatar' => $otherUser->avatar ?? null,
                'email' => $otherUser->email ?? null,
                'phone' => $otherUser->phone ?? null,
                'lastMessage' => $conv->lastMessage ? [
                    'text' => $conv->lastMessage->text,
                    'timestamp' => $conv->lastMessage->created_at,
                    'isOwner' => $conv->lastMessage->sender_id === $conv->owner_id,
                    'status' => $conv->lastMessage->status,
                ] : null,
                'unread' => false,
                'property' => $conv->property ? [
                    'name' => $conv->property->name,
                    'address' => $conv->property->address ?? null,
                    'city' => $conv->property->city ?? null,
                ] : null,
                'reservation' => $reservation ? [
                    'id' => $reservation->id,
                    'reservation_date' => $reservation->reservation_date ?? null,
                    'checkin' => $reservation->checkin ?? null,
                    'checkout' => $reservation->checkout ?? null,
                    'guests' => $reservation->guests ?? null,
                    'paymentStatus' => $reservation->payment_status ?? null,
                ] : null,
                'owner_id' => $conv->owner_id,
                'guest_id' => $conv->guest_id,
                'user_one_id' => $conv->guest_id,
                'user_two_id' => $conv->owner_id,
            ];
        });

        return response()->json($mapped);
    }

    /**
     * @OA\Post(
     *     path="/api/conversations",
     *     summary="Crear una nueva conversación a partir de una reserva",
     *     tags={"Conversaciones"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"reservation_id"},
     *             @OA\Property(property="reservation_id", type="integer", example=10)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Conversación creada o ya existente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="reservation_id", type="integer", example=10),
     *             @OA\Property(property="property_id", type="integer", example=5),
     *             @OA\Property(property="guest_id", type="integer", example=3),
     *             @OA\Property(property="owner_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="No autorizado para esta conversación.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="No autorizado para esta conversación.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
        ]);

        $reservation = Reservation::with('property')->findOrFail($request->reservation_id);

        $guestId = $reservation->user_id;
        $propertyId = $reservation->property_id;
        $ownerId = $reservation->property->user_id;

        $currentUser = Auth::id();
        if ($currentUser !== $guestId && $currentUser !== $ownerId) {
            abort(403, 'No autorizado para esta conversación.');
        }

        $conversation = Conversation::where('reservation_id', $reservation->id)->first();
        if (!$conversation) {
            $conversation = Conversation::create([
                'reservation_id' => $reservation->id,
                'property_id' => $propertyId,
                'guest_id' => $guestId,
                'owner_id' => $ownerId,
            ]);
        }

        return response()->json($conversation, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/conversations/{id}",
     *     summary="Mostrar una conversación y sus mensajes",
     *     tags={"Conversaciones"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la conversación",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalle de la conversación con sus mensajes",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="property_id", type="integer", example=5),
     *             @OA\Property(property="guest_id", type="integer", example=3),
     *             @OA\Property(property="owner_id", type="integer", example=2),
     *             @OA\Property(
     *                 property="messages",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="sender_id", type="integer", example=3),
     *                     @OA\Property(
     *                         property="sender",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=3),
     *                         @OA\Property(property="name", type="string", example="Juan Pérez"),
     *                         @OA\Property(property="avatar", type="string", example="https://example.com/avatar.jpg")
     *                     ),
     *                     @OA\Property(property="text", type="string", example="Hola"),
     *                     @OA\Property(property="created_at", type="string", example="2024-07-01 13:00:00"),
     *                     @OA\Property(property="status", type="string", example="read")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="property",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=5),
     *                 @OA\Property(property="name", type="string", example="Apartamento Centro"),
     *                 @OA\Property(property="address", type="string", example="Calle Mayor 1"),
     *                 @OA\Property(property="city", type="string", example="Madrid")
     *             ),
     *             @OA\Property(
     *                 property="guest",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=3),
     *                 @OA\Property(property="name", type="string", example="Juan Pérez"),
     *                 @OA\Property(property="avatar", type="string", example="https://example.com/avatar.jpg")
     *             ),
     *             @OA\Property(
     *                 property="owner",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=2),
     *                 @OA\Property(property="name", type="string", example="Ana López"),
     *                 @OA\Property(property="avatar", type="string", example="https://example.com/avatar2.jpg")
     *             ),
     *             @OA\Property(
     *                 property="reservation",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=10),
     *                 @OA\Property(property="reservation_date", type="string", example="2024-07-01"),
     *                 @OA\Property(property="checkin", type="string", example="2024-07-10"),
     *                 @OA\Property(property="checkout", type="string", example="2024-07-15"),
     *                 @OA\Property(property="guests", type="integer", example=2),
     *                 @OA\Property(property="payment_status", type="string", example="paid")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="No autorizado para ver esta conversación.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="No autorizado para ver esta conversación.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Conversación no encontrada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="No encontrado")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $conversation = Conversation::with(['messages.sender', 'property', 'guest', 'owner', 'reservation'])
            ->findOrFail($id);

        $userId = Auth::id();
        if ($conversation->guest_id !== $userId && $conversation->owner_id !== $userId) {
            abort(403);
        }

        return response()->json($conversation);
    }
}
