<?php
// routes/channels.php
//
// Canales de broadcasting para Laravel Echo y Pusher.
// Estos canales permiten comunicación en tiempo real en la aplicación.
//

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;
use App\Models\Conversation;

// Canal privado para un usuario específico (por ID)
// Ejemplo: user.5
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Canal de usuarios en línea (puedes emitir eventos de presencia)
// Ejemplo: online
Broadcast::channel('online', function ($user) {
    return ['id' => $user->id, 'name' => $user->name];
});

// Canal general de chat (todos los autenticados pueden escuchar)
// Ejemplo: chat
Broadcast::channel('chat', function ($user) {
    return true;
});

// Canal privado para una conversación concreta
// Ejemplo: chat.12 (donde 12 es el ID de la conversación)
//
// Lógica:
// - Solo pueden escuchar los usuarios que son participantes de la conversación
// - Se hace log si la conversación no existe o si el usuario no es participante
Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
    $conversation = Conversation::find($conversationId);

    if (!$conversation) {
        Log::warning("Conversación {$conversationId} no encontrada para usuario {$user->id}");
        return false;
    }

    $isParticipant = in_array($user->id, [$conversation->user_one_id, $conversation->user_two_id]);

    if (!$isParticipant) {
        Log::warning("Usuario {$user->id} no autorizado para conversación {$conversationId}");
        return false;
    }

    Log::info("Usuario {$user->id} autorizado para conversación {$conversationId}");

    return [
        'id' => $user->id,
        'name' => $user->name,
        'conversation_id' => $conversationId
    ];
});
