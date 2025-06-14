<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SupportController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/support/contact",
     *     summary="Enviar mensaje de contacto al soporte",
     *     tags={"Soporte"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"subject", "message", "email"},
     *             @OA\Property(property="subject", type="string", example="Problema con la reserva"),
     *             @OA\Property(property="message", type="string", example="Tengo un problema al intentar reservar una propiedad."),
     *             @OA\Property(property="email", type="string", example="usuario@email.com"),
     *             @OA\Property(property="urgent", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mensaje enviado correctamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Mensaje enviado.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error de validación."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error del servidor",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error del servidor: ...")
     *         )
     *     )
     * )
     */
    public function contact(Request $request)
    {
        try {
            $validated = $request->validate([
                'subject' => 'required|string',
                'message' => 'required|string',
                'email'   => 'required|email',
                'urgent'  => 'boolean',
            ]);

            Mail::raw(
                "De: {$validated['email']}\n\nAsunto: {$validated['subject']}\n\nMensaje: {$validated['message']}\n\nUrgente: " . ($validated['urgent'] ? 'Sí' : 'No'),
                function($mail) use ($validated) {
                    $mail->to('soporte@tuservicio.com')
                         ->subject('Soporte: ' . $validated['subject']);
                }
            );

            return response()->json(['success' => true, 'message' => 'Mensaje enviado.']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error del servidor: ' . $e->getMessage()
            ], 500);
        }
    }
}

?>
