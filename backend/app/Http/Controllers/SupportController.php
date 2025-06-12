<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SupportController extends Controller
{
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
