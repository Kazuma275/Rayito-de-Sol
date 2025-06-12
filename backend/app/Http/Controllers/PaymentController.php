<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class PaymentController extends Controller
{
    public function createPaymentIntent(Request $request)
    {
        try {
            // Validar la solicitud
            $request->validate([
                'amount' => 'required|integer|min:50', // Mínimo 50 céntimos
                'currency' => 'required|string|in:eur,usd',
                'property_id' => 'required|exists:properties,id',
                'booking_data' => 'required|array',
            ]);

            // Configurar Stripe con la clave secreta
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            // Crear el Payment Intent
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $request->amount,
                'currency' => $request->currency,
                'metadata' => [
                    'property_id' => $request->property_id,
                    'guest_email' => $request->booking_data['guestInfo']['email'] ?? '',
                    'checkin_date' => $request->booking_data['checkinDate'] ?? '',
                    'checkout_date' => $request->booking_data['checkoutDate'] ?? '',
                    'guests' => $request->booking_data['guests'] ?? 1,
                ],
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);

            // Devolver la respuesta
            return response()->json([
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
            ]);

        } catch (Exception $e) {
            // Registrar el error detallado
            Log::error('Error creating payment intent: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);

            // Devolver una respuesta de error
            return response()->json([
                'error' => 'Error al crear la intención de pago',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function confirmPayment(Request $request)
    {
        try {
            $request->validate([
                'payment_intent_id' => 'required|string',
            ]);

            // Configurar Stripe
            \Stripe\Stripe::setApiKey(env('STRIPE_LIVE'));

            // Recuperar el Payment Intent
            $paymentIntent = \Stripe\PaymentIntent::retrieve($request->payment_intent_id);

            return response()->json([
                'status' => $paymentIntent->status,
                'payment_intent' => $paymentIntent,
            ]);

        } catch (Exception $e) {
            Log::error('Error confirming payment: ' . $e->getMessage());

            return response()->json([
                'error' => 'Error al confirmar el pago',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
