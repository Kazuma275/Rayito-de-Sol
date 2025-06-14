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
    /**
     * @OA\Post(
     *     path="/api/messages/send",
     *     summary="Enviar mensaje (texto y/o adjunto)",
     *     tags={"Mensajes"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"conversation_id"},
     *                 @OA\Property(property="conversation_id", type="integer", example=1),
     *                 @OA\Property(property="text", type="string", example="Hola, ¿cómo estás?"),
     *                 @OA\Property(
     *                     property="attachment",
     *                     type="string",
     *                     format="binary",
     *                     description="Archivo adjunto, máximo 10MB"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mensaje enviado correctamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="conversation_id", type="integer", example=1),
     *                 @OA\Property(property="sender_id", type="integer", example=2),
     *                 @OA\Property(property="text", type="string", example="Hola, ¿cómo estás?"),
     *                 @OA\Property(property="attachment", type="string", example="attachments/archivo.pdf"),
     *                 @OA\Property(property="status", type="string", example="sent"),
     *                 @OA\Property(property="created_at", type="string", example="2024-07-01 12:00:00")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="No autorizado")
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
     *     )
     * )
     */
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
            'sender_id' => $userId,
            'text' => $request->text,
            'attachment' => $attachmentPath,
            'status' => 'sent',
        ]);

        // Emitir el evento para el canal privado chat.{conversation_id}
        broadcast(new MessageSent($message, $conversation->id))->toOthers();

        return response()->json(['message' => $message]);
    }

    /**
     * @OA\Post(
     *     path="/api/conversations/{conversation}/typing",
     *     summary="Emitir evento de typing en una conversación",
     *     tags={"Mensajes"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="conversation",
     *         in="path",
     *         required=true,
     *         description="ID de la conversación",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Evento de typing emitido",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="No autorizado")
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
    public function typing(Request $request, $conversation)
    {
        $conversation = Conversation::findOrFail($conversation);
        $userId = auth()->id();

        if (!in_array($userId, [$conversation->user_one_id, $conversation->user_two_id])) {
            abort(403, 'No autorizado');
        }

        broadcast(new UserTyping($userId, $conversation->id))->toOthers();

        return response()->json(['success' => true]);
    }

    /**
     * @OA\Get(
     *     path="/api/conversations/{conversation}/messages",
     *     summary="Obtener mensajes de una conversación",
     *     tags={"Mensajes"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="conversation",
     *         in="path",
     *         required=true,
     *         description="ID de la conversación",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de mensajes",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="conversation_id", type="integer", example=1),
     *                 @OA\Property(property="sender_id", type="integer", example=2),
     *                 @OA\Property(property="text", type="string", example="Hola, ¿cómo estás?"),
     *                 @OA\Property(property="attachment", type="string", example="attachments/archivo.pdf"),
     *                 @OA\Property(property="status", type="string", example="sent"),
     *                 @OA\Property(property="created_at", type="string", example="2024-07-01 12:00:00"),
     *                 @OA\Property(property="read_at", type="string", example="2024-07-01 12:00:05")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="No autorizado")
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
    public function index($conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);

        $userId = Auth::id();
        if (!in_array($userId, [$conversation->user_one_id, $conversation->user_two_id])) {
            abort(403, 'No autorizado');
        }

        $messages = Message::where('conversation_id', $conversationId)
            ->orderBy('created_at')
            ->get();

        return response()->json($messages);
    }

    /**
     * @OA\Post(
     *     path="/api/conversations/{conversation}/mark-as-read",
     *     summary="Marcar mensajes de una conversación como leídos",
     *     tags={"Mensajes"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="conversation",
     *         in="path",
     *         required=true,
     *         description="ID de la conversación",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mensajes marcados como leídos",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="No autorizado")
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
    public function markAsRead(Request $request, $conversation)
    {
        $conversation = Conversation::findOrFail($conversation);
        $userId = auth()->id();

        if (!in_array($userId, [$conversation->user_one_id, $conversation->user_two_id])) {
            abort(403, 'No autorizado');
        }

        $conversation->messages()
            ->where('sender_id', '!=', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }
}
