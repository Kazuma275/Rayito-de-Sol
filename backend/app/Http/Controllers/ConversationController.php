<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Reservation;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    // Obtener todas las conversaciones del usuario autenticado (como huésped o propietario)
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
                'unread' => false, // puedes personalizar esto según tus necesidades
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

    // Crear una nueva conversación (si no existe ya) según una reserva
    public function store(Request $request)
    {
        $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
        ]);

        $reservation = Reservation::with('property')->findOrFail($request->reservation_id);

        $guestId = $reservation->user_id;
        $propertyId = $reservation->property_id;
        $ownerId = $reservation->property->user_id;

        // Solo el huésped o el propietario pueden crear/conversar
        $currentUser = Auth::id();
        if ($currentUser !== $guestId && $currentUser !== $ownerId) {
            abort(403, 'No autorizado para esta conversación.');
        }

        // Solo una conversación por reserva
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

    // Mostrar una conversación y sus mensajes
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
