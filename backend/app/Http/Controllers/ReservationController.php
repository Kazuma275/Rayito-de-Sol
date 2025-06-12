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
    public function getUsers()
    {
        return response()->json(\App\Models\User::select('id', 'name')->get());
    }

    public function __construct()
    {
        // TU CLAVE SECRETA DE PRUEBA
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    // Método para mostrar las reservas del usuario autenticado
    public function index()
    {
        // Si hay usuario autenticado, mostrar solo sus reservas
        if (Auth::check()) {
            $reservations = Reservation::where('user_id', Auth::id())
                ->with(['property' => function ($query) {
                    $query->select('id', 'name', 'location', 'image', 'price', 'bedrooms', 'capacity');
                }])
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Si no hay usuario autenticado, devolver array vacío
            $reservations = collect([]);
        }

        return response()->json($reservations);
    }

    // Método para mostrar TODAS las reservas (solo para admin)
    public function all()
    {
        $reservations = Reservation::with(['property' => function ($query) {
            $query->select('id', 'name', 'location', 'image', 'price', 'bedrooms', 'capacity');
        }])->orderBy('created_at', 'desc')->get();

        return response()->json($reservations);
    }

    // Resto de métodos...
    public function show($id)
    {
        $reservation = Reservation::with('property')->findOrFail($id);

        // Verificar que el usuario puede ver esta reserva
        if (Auth::check() && $reservation->user_id !== Auth::id()) {
            // También permitir si es el propietario de la propiedad
            if ($reservation->property->user_id !== Auth::id()) {
                return response()->json(['error' => 'No autorizado'], 403);
            }
        }

        return response()->json($reservation);
    }

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

            // Verificar que el pago fue exitoso
            $paymentIntent = \Stripe\PaymentIntent::retrieve($request->payment_intent_id);

            if ($paymentIntent->status !== 'succeeded') {
                return response()->json([
                    'error' => 'El pago no se ha completado correctamente'
                ], 400);
            }

            // Verificar que la propiedad existe
            $property = Property::findOrFail($request->property_id);

            // Crear la reserva
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

            // Cargar la relación con la propiedad
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

    public function update(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        // Verificar que el usuario puede actualizar esta reserva
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

    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);

        // Verificar que el usuario puede eliminar esta reserva
        if (Auth::check() && $reservation->user_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $reservation->delete();

        return response()->json([
            'message' => 'Reserva eliminada exitosamente'
        ]);
    }

    public function cancel(Request $request, $id)
    {
        try {
            $reservation = Reservation::findOrFail($id);

            // Verificar que el usuario puede cancelar esta reserva
            if (Auth::check() && $reservation->user_id !== Auth::id()) {
                return response()->json(['error' => 'No autorizado'], 403);
            }

            // Actualizar el estado en los detalles
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

    public function getByProperty($propertyId)
    {
        $reservations = Reservation::where('property_id', $propertyId)
            ->with('property')
            ->orderBy('reservation_date', 'desc')
            ->get();

        return response()->json($reservations);
    }

    public function getByUser($userId)
    {
        // Verificar que el usuario puede ver estas reservas
        if (Auth::check() && Auth::id() != $userId) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $reservations = Reservation::where('user_id', $userId)
            ->with('property')
            ->orderBy('reservation_date', 'desc')
            ->get();

        return response()->json($reservations);
    }

    // Métodos adicionales para propietarios
    public function ownerBookings()
    {
        $user = Auth::user();

        $reservations = Reservation::whereHas('property', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with('property')->orderBy('created_at', 'desc')->get();

        return response()->json($reservations);
    }

    public function propertyBookings($propertyId)
    {
        $property = Property::findOrFail($propertyId);

        // Verificar que el usuario es el propietario
        if ($property->user_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $reservations = Reservation::where('property_id', $propertyId)
            ->with('property')
            ->orderBy('reservation_date', 'desc')
            ->get();

        return response()->json($reservations);
    }

    public function accept(Request $request, $id)
    {
        try {
            $reservation = Reservation::findOrFail($id);

            // Verificar que el usuario es el propietario
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

    public function reject(Request $request, $id)
    {
        try {
            $reservation = Reservation::findOrFail($id);

            // Verificar que el usuario es el propietario
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
