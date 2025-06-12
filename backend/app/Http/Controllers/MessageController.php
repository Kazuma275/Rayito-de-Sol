<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Conversation;
use App\Events\MessageSent;
use App\Events\UserTyping;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    // Enviar mensaje (texto y/o adjunto)
    public function send(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'text' => 'nullable|string',
            'attachment' => 'nullable|file|max:10240', // 10MB
        ]);

        $conversation = Conversation::findOrFail($request->conversation_id);

        // Solo participantes pueden enviar mensaje
        $userId = Auth::id();
        if (!in_array($userId, [$conversation->user_one_id, $conversation->user_two_id])) {
            abort(403, 'No autorizado');
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attachments', 'public');
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $userId, // Asegúrate que tu modelo Message tiene este campo
            'text' => $request->text,
            'attachment' => $attachmentPath,
            'status' => 'sent',
        ]);

        // Emitir el evento para el canal privado chat.{conversation_id}
        broadcast(new MessageSent($message, $conversation->id))->toOthers();

        return response()->json(['message' => $message]);
    }

    // Enviar evento de typing
    public function typing(Request $request, $conversation)
{
    $conversation = Conversation::findOrFail($conversation);
    $userId = auth()->id();

    // Verificar que el usuario es parte de la conversación
    if (!in_array($userId, [$conversation->user_one_id, $conversation->user_two_id])) {
        abort(403, 'No autorizado');
    }

    // Emitir evento de typing
    broadcast(new UserTyping($userId, $conversation->id))->toOthers();

    return response()->json(['success' => true]);
}

    public function index($conversationId)
    {
        // Verifica que la conversación existe
        $conversation = Conversation::findOrFail($conversationId);

        // Solo los participantes pueden ver los mensajes
        $userId = Auth::id();
        if (!in_array($userId, [$conversation->user_one_id, $conversation->user_two_id])) {
            abort(403, 'No autorizado');
        }

        // Obtén los mensajes ordenados por fecha de creación
        $messages = Message::where('conversation_id', $conversationId)
            ->orderBy('created_at')
            ->get();

        return response()->json($messages);
    }

public function markAsRead(Request $request, $conversation)
{
    $conversation = Conversation::findOrFail($conversation);
    $userId = auth()->id();

    // Verificar que el usuario es parte de la conversación
    if (!in_array($userId, [$conversation->user_one_id, $conversation->user_two_id])) {
        abort(403, 'No autorizado');
    }

    // Marcar mensajes como leídos
    $conversation->messages()
        ->where('sender_id', '!=', $userId)
        ->whereNull('read_at')
        ->update(['read_at' => now()]);

    return response()->json(['success' => true]);
}


}
