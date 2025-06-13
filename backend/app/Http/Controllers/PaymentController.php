<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class PaymentController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/payments/intent",
     *     summary="Crear una intención de pago (Stripe PaymentIntent)",
     *     tags={"Pagos"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"amount", "currency", "property_id", "booking_data"},
     *             @OA\Property(property="amount", type="integer", example=1000, description="Cantidad en céntimos"),
     *             @OA\Property(property="currency", type="string", enum={"eur","usd"}, example="eur"),
     *             @OA\Property(property="property_id", type="integer", example=7),
     *             @OA\Property(
     *                 property="booking_data",
     *                 type="object",
     *                 @OA\Property(property="guestInfo", type="object",
     *                     @OA\Property(property="email", type="string", example="guest@example.com")
     *                 ),
     *                 @OA\Property(property="checkinDate", type="string", example="2024-07-01"),
     *                 @OA\Property(property="checkoutDate", type="string", example="2024-07-07"),
     *                 @OA\Property(property="guests", type="integer", example=2)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Intención de pago creada correctamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="client_secret", type="string", example="pi_1NAb0a2eZvKYlo2C5s...secret_123"),
     *             @OA\Property(property="payment_intent_id", type="string", example="pi_1NAb0a2eZvKYlo2C5s")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Error al crear la intención de pago"),
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error en la creación de la intención de pago",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Error al crear la intención de pago"),
     *             @OA\Property(property="message", type="string", example="Stripe error message")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/payments/confirm",
     *     summary="Confirmar el estado de una intención de pago",
     *     tags={"Pagos"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"payment_intent_id"},
     *             @OA\Property(property="payment_intent_id", type="string", example="pi_1NAb0a2eZvKYlo2C5s")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Estado de la intención de pago",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="succeeded"),
     *             @OA\Property(
     *                 property="payment_intent",
     *                 type="object",
     *                 @OA\Property(property="id", type="string", example="pi_1NAb0a2eZvKYlo2C5s"),
     *                 @OA\Property(property="amount", type="integer", example=1000),
     *                 @OA\Property(property="currency", type="string", example="eur"),
     *                 @OA\Property(property="status", type="string", example="succeeded")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Error al confirmar el pago"),
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error en la confirmación del pago",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Error al confirmar el pago"),
     *             @OA\Property(property="message", type="string", example="Stripe error message")
     *         )
     *     )
     * )
     */
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
