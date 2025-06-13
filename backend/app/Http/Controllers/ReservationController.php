<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReservationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="Obtener lista de usuarios (id y nombre)",
     *     tags={"Reservas"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de usuarios",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Juan Pérez")
     *             )
     *         )
     *     )
     * )
     */
    public function getUsers()
    {
        return response()->json(\App\Models\User::select('id', 'name')->get());
    }

    public function __construct()
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    /**
     * @OA\Get(
     *     path="/api/reservations",
     *     summary="Listar reservas del usuario autenticado",
     *     tags={"Reservas"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Listado de reservas",
     *         @OA\JsonContent(type="array", @OA\Items(type="object"))
     *     )
     * )
     */
    public function index()
    {
        if (Auth::check()) {
            $reservations = Reservation::where('user_id', Auth::id())
                ->with(['property' => function ($query) {
                    $query->select('id', 'name', 'location', 'image', 'price', 'bedrooms', 'capacity');
                }])
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $reservations = collect([]);
        }

        return response()->json($reservations);
    }

    /**
     * @OA\Get(
     *     path="/api/reservations/all",
     *     summary="Listar todas las reservas (solo admin)",
     *     tags={"Reservas"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Listado de todas las reservas",
     *         @OA\JsonContent(type="array", @OA\Items(type="object"))
     *     )
     * )
     */
    public function all()
    {
        $reservations = Reservation::with(['property' => function ($query) {
            $query->select('id', 'name', 'location', 'image', 'price', 'bedrooms', 'capacity');
        }])->orderBy('created_at', 'desc')->get();

        return response()->json($reservations);
    }

    /**
     * @OA\Get(
     *     path="/api/reservations/{id}",
     *     summary="Obtener los detalles de una reserva",
     *     tags={"Reservas"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la reserva",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles de la reserva",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="No autorizado",
     *         @OA\JsonContent(@OA\Property(property="error", type="string", example="No autorizado"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Reserva no encontrada",
     *         @OA\JsonContent(@OA\Property(property="error", type="string", example="No encontrada"))
     *     )
     * )
     */
    public function show($id)
    {
        $reservation = Reservation::with('property')->findOrFail($id);

        if (Auth::check() && $reservation->user_id !== Auth::id()) {
            if ($reservation->property->user_id !== Auth::id()) {
                return response()->json(['error' => 'No autorizado'], 403);
            }
        }

        return response()->json($reservation);
    }

    /**
     * @OA\Post(
     *     path="/api/reservations",
     *     summary="Crear una nueva reserva",
     *     tags={"Reservas"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"property_id","payment_intent_id","checkin_date","checkout_date","guests","guest_name","guest_email","guest_phone","total_price","total_nights","subtotal","service_fee","taxes"},
     *             @OA\Property(property="property_id", type="integer", example=1),
     *             @OA\Property(property="payment_intent_id", type="string", example="pi_1NAb0a2eZvKYlo2C5s"),
     *             @OA\Property(property="checkin_date", type="string", format="date", example="2024-07-01"),
     *             @OA\Property(property="checkout_date", type="string", format="date", example="2024-07-10"),
     *             @OA\Property(property="guests", type="integer", example=2),
     *             @OA\Property(property="guest_name", type="string", example="Juan Pérez"),
     *             @OA\Property(property="guest_email", type="string", example="juan@example.com"),
     *             @OA\Property(property="guest_phone", type="string", example="34123456789"),
     *             @OA\Property(property="total_price", type="number", format="float", example=1200.50),
     *             @OA\Property(property="total_nights", type="integer", example=3),
     *             @OA\Property(property="subtotal", type="number", format="float", example=1100.00),
     *             @OA\Property(property="service_fee", type="number", format="float", example=50.00),
     *             @OA\Property(property="taxes", type="number", format="float", example=50.50)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Reserva creada exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="booking_reference", type="string"),
     *             @OA\Property(property="status", type="string"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="reservation", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Pago no completado",
     *         @OA\JsonContent(@OA\Property(property="error", type="string", example="El pago no se ha completado correctamente"))
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Error al crear la reserva"),
     *             @OA\Property(property="message", type="string", example="The given data was invalid.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Error al crear la reserva"),
     *             @OA\Property(property="message", type="string", example="Error message")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'property_id' => 'required|exists:properties,id',
                'payment_intent_id' => 'required|string',
                'checkin_date' => 'required|date|after:today',
                'checkout_date' => 'required|date|after:checkin_date',
                'guests' => 'required|integer|min:1',
                'guest_name' => 'required|string|max:255',
                'guest_email' => 'required|email',
                'guest_phone' => 'required|string|max:20',
                'total_price' => 'required|numeric|min:0',
                'total_nights' => 'required|integer|min:1',
                'subtotal' => 'required|numeric|min:0',
                'service_fee' => 'required|numeric|min:0',
                'taxes' => 'required|numeric|min:0',
            ]);

            $paymentIntent = \Stripe\PaymentIntent::retrieve($request->payment_intent_id);

            if ($paymentIntent->status !== 'succeeded') {
                return response()->json([
                    'error' => 'El pago no se ha completado correctamente'
                ], 400);
            }

            $property = Property::findOrFail($request->property_id);

            $reservation = Reservation::create([
                'user_id' => Auth::id() ?? null,
                'property_id' => $request->property_id,
                'reservation_date' => Carbon::parse($request->checkin_date)->format('Y-m-d'),
                'reservation_time' => '15:00:00',
                'details' => [
                    'check_in' => $request->checkin_date,
                    'check_out' => $request->checkout_date,
                    'guests' => $request->guests,
                    'guest_name' => $request->guest_name,
                    'guest_email' => $request->guest_email,
                    'guest_phone' => $request->guest_phone,
                    'total_price' => $request->total_price,
                    'total_nights' => $request->total_nights,
                    'subtotal' => $request->subtotal,
                    'service_fee' => $request->service_fee,
                    'taxes' => $request->taxes,
                    'status' => 'confirmed',
                    'payment_intent_id' => $request->payment_intent_id,
                    'booking_reference' => $this->generateBookingReference(),
                    'created_via' => 'stripe_payment',
                    'payment_mode' => 'test',
                    'stripe_payment_id' => $paymentIntent->id,
                    'payment_amount' => $paymentIntent->amount,
                    'payment_currency' => $paymentIntent->currency,
                ],
            ]);

            $reservation->load('property');

            return response()->json([
                'id' => $reservation->id,
                'booking_reference' => $reservation->details['booking_reference'],
                'status' => $reservation->details['status'],
                'message' => 'Reserva creada exitosamente (MODO PRUEBA)',
                'reservation' => $reservation
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating reservation: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);

            return response()->json([
                'error' => 'Error al crear la reserva',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/reservations/{id}",
     *     summary="Actualizar una reserva",
     *     tags={"Reservas"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la reserva",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="reservation_date", type="string", format="date", example="2024-07-01"),
     *             @OA\Property(property="reservation_time", type="string", example="15:00:00"),
     *             @OA\Property(property="details", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reserva actualizada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Reserva actualizada exitosamente"),
     *             @OA\Property(property="reservation", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="No autorizado",
     *         @OA\JsonContent(@OA\Property(property="error", type="string", example="No autorizado"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Reserva no encontrada",
     *         @OA\JsonContent(@OA\Property(property="error", type="string", example="No encontrada"))
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        if (Auth::check() && $reservation->user_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $request->validate([
            'reservation_date' => 'sometimes|date',
            'reservation_time' => 'sometimes|date_format:H:i:s',
            'details' => 'sometimes|array',
        ]);

        $reservation->update($request->only(['reservation_date', 'reservation_time', 'details']));

        return response()->json([
            'message' => 'Reserva actualizada exitosamente',
            'reservation' => $reservation
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/reservations/{id}",
     *     summary="Eliminar una reserva",
     *     tags={"Reservas"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la reserva",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reserva eliminada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Reserva eliminada exitosamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="No autorizado",
     *         @OA\JsonContent(@OA\Property(property="error", type="string", example="No autorizado"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Reserva no encontrada",
     *         @OA\JsonContent(@OA\Property(property="error", type="string", example="No encontrada"))
     *     )
     * )
     */
    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);

        if (Auth::check() && $reservation->user_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $reservation->delete();

        return response()->json([
            'message' => 'Reserva eliminada exitosamente'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/reservations/{id}/cancel",
     *     summary="Cancelar una reserva",
     *     tags={"Reservas"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la reserva",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="reason", type="string", example="Imprevisto personal")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reserva cancelada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Reserva cancelada exitosamente"),
     *             @OA\Property(property="reservation", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="No autorizado",
     *         @OA\JsonContent(@OA\Property(property="error", type="string", example="No autorizado"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Reserva no encontrada",
     *         @OA\JsonContent(@OA\Property(property="error", type="string", example="No encontrada"))
     *     )
     * )
     */
    public function cancel(Request $request, $id)
    {
        try {
            $reservation = Reservation::findOrFail($id);

            if (Auth::check() && $reservation->user_id !== Auth::id()) {
                return response()->json(['error' => 'No autorizado'], 403);
            }

            $details = $reservation->details;
            $details['status'] = 'cancelled';
            $details['cancelled_at'] = now()->toISOString();
            $details['cancellation_reason'] = $request->input('reason', 'Cancelado por el usuario');

            $reservation->update(['details' => $details]);

            return response()->json([
                'message' => 'Reserva cancelada exitosamente',
                'reservation' => $reservation
            ]);
        } catch (\Exception $e) {
            Log::error('Error cancelling reservation: ' . $e->getMessage());

            return response()->json([
                'error' => 'Error al cancelar la reserva',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function generateBookingReference()
    {
        return 'BK' . strtoupper(uniqid());
    }

    /**
     * @OA\Get(
     *     path="/api/properties/{propertyId}/reservations",
     *     summary="Obtener reservas de una propiedad",
     *     tags={"Reservas"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="propertyId",
     *         in="path",
     *         required=true,
     *         description="ID de la propiedad",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reservas de la propiedad",
     *         @OA\JsonContent(type="array", @OA\Items(type="object"))
     *     )
     * )
     */
    public function getByProperty($propertyId)
    {
        $reservations = Reservation::where('property_id', $propertyId)
            ->with('property')
            ->orderBy('reservation_date', 'desc')
            ->get();

        return response()->json($reservations);
    }

    /**
     * @OA\Get(
     *     path="/api/users/{userId}/reservations",
     *     summary="Obtener reservas de un usuario",
     *     tags={"Reservas"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         description="ID del usuario",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reservas del usuario",
     *         @OA\JsonContent(type="array", @OA\Items(type="object"))
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="No autorizado",
     *         @OA\JsonContent(@OA\Property(property="error", type="string", example="No autorizado"))
     *     )
     * )
     */
    public function getByUser($userId)
    {
        if (Auth::check() && Auth::id() != $userId) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $reservations = Reservation::where('user_id', $userId)
            ->with('property')
            ->orderBy('reservation_date', 'desc')
            ->get();

        return response()->json($reservations);
    }

    /**
     * @OA\Get(
     *     path="/api/owner/reservations",
     *     summary="Obtener reservas de propiedades del propietario autenticado",
     *     tags={"Reservas"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Reservas de propiedades del propietario",
     *         @OA\JsonContent(type="array", @OA\Items(type="object"))
     *     )
     * )
     */
    public function ownerBookings()
    {
        $user = Auth::user();

        $reservations = Reservation::whereHas('property', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with('property')->orderBy('created_at', 'desc')->get();

        return response()->json($reservations);
    }

    /**
     * @OA\Get(
     *     path="/api/properties/{propertyId}/bookings",
     *     summary="Obtener reservas de una propiedad (solo propietario)",
     *     tags={"Reservas"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="propertyId",
     *         in="path",
     *         required=true,
     *         description="ID de la propiedad",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reservas de la propiedad",
     *         @OA\JsonContent(type="array", @OA\Items(type="object"))
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="No autorizado",
     *         @OA\JsonContent(@OA\Property(property="error", type="string", example="No autorizado"))
     *     )
     * )
     */
    public function propertyBookings($propertyId)
    {
        $property = Property::findOrFail($propertyId);

        if ($property->user_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $reservations = Reservation::where('property_id', $propertyId)
            ->with('property')
            ->orderBy('reservation_date', 'desc')
            ->get();

        return response()->json($reservations);
    }

    /**
     * @OA\Post(
     *     path="/api/reservations/{id}/accept",
     *     summary="Aceptar una reserva (solo propietario)",
     *     tags={"Reservas"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la reserva",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reserva aceptada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Reserva aceptada exitosamente"),
     *             @OA\Property(property="reservation", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="No autorizado",
     *         @OA\JsonContent(@OA\Property(property="error", type="string", example="No autorizado"))
     *     )
     * )
     */
    public function accept(Request $request, $id)
    {
        try {
            $reservation = Reservation::findOrFail($id);

            if ($reservation->property->user_id !== Auth::id()) {
                return response()->json(['error' => 'No autorizado'], 403);
            }

            $details = $reservation->details;
            $details['status'] = 'confirmed';
            $details['confirmed_at'] = now()->toISOString();

            $reservation->update(['details' => $details]);

            return response()->json([
                'message' => 'Reserva aceptada exitosamente',
                'reservation' => $reservation
            ]);
        } catch (\Exception $e) {
            Log::error('Error accepting reservation: ' . $e->getMessage());

            return response()->json([
                'error' => 'Error al aceptar la reserva',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/reservations/{id}/reject",
     *     summary="Rechazar una reserva (solo propietario)",
     *     tags={"Reservas"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la reserva",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="reason", type="string", example="Motivo de rechazo")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reserva rechazada",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Reserva rechazada"),
     *             @OA\Property(property="reservation", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="No autorizado",
     *         @OA\JsonContent(@OA\Property(property="error", type="string", example="No autorizado"))
     *     )
     * )
     */
    public function reject(Request $request, $id)
    {
        try {
            $reservation = Reservation::findOrFail($id);

            if ($reservation->property->user_id !== Auth::id()) {
                return response()->json(['error' => 'No autorizado'], 403);
            }

            $details = $reservation->details;
            $details['status'] = 'rejected';
            $details['rejected_at'] = now()->toISOString();
            $details['rejection_reason'] = $request->input('reason', 'Rechazado por el propietario');

            $reservation->update(['details' => $details]);

            return response()->json([
                'message' => 'Reserva rechazada',
                'reservation' => $reservation
            ]);
        } catch (\Exception $e) {
            Log::error('Error rejecting reservation: ' . $e->getMessage());

            return response()->json([
                'error' => 'Error al rechazar la reserva',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
