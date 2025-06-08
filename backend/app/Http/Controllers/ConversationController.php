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
        $conversations = Conversation::with(['property', 'guest', 'owner', 'lastMessage'])
            ->where('guest_id', $userId)
            ->orWhere('owner_id', $userId)
            ->orderByDesc('updated_at')
            ->get();

        return response()->json($conversations);
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
        $conversation = Conversation::with(['messages.sender', 'property', 'guest', 'owner'])
            ->findOrFail($id);

        $userId = Auth::id();
        if ($conversation->guest_id !== $userId && $conversation->owner_id !== $userId) {
            abort(403);
        }

        return response()->json($conversation);
    }
}
