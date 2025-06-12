<?php
// routes/channels.php - CORREGIDO

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;
use App\Models\Conversation;

// Canal para usuarios específicos
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Canal para usuarios en línea
Broadcast::channel('online', function ($user) {
    return ['id' => $user->id, 'name' => $user->name];
});

// Canal general de chat (si lo necesitas)
Broadcast::channel('chat', function ($user) {
    return true;
});

// 🚨 ESTE ES EL IMPORTANTE - Canal para conversaciones específicas
Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
    // Buscar la conversación
    $conversation = Conversation::find($conversationId);

    // Verificar que la conversación existe y el usuario es participante
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

    // Retornar información del usuario para el canal
    return [
        'id' => $user->id,
        'name' => $user->name,
        'conversation_id' => $conversationId
    ];
});
