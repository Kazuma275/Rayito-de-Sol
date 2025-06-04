<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;

class ReservationController extends Controller
{
    /**
     * Devuelve las reservas de una propiedad específica.
     */
    public function propertyBookings($id)
    {
        $reservations = Reservation::where('property_id', $id)
            ->with(['property', 'user'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($reservation) {
                $details = [];
                if ($reservation->details) {
                    $details = json_decode($reservation->details, true) ?: [];
                }
                return [
                    'id' => $reservation->id,
                    'propertyId' => $reservation->property_id,
                    'userId' => $reservation->user_id,
                    'checkIn' => $details['check_in'] ?? ($reservation->reservation_date . 'T' . $reservation->reservation_time),
                    'checkOut' => $details['check_out'] ?? null,
                    'guests' => $details['guests'] ?? null,
                    'status' => $details['status'] ?? null,
                    'createdAt' => $reservation->created_at,
                    'property' => $reservation->property,
                    'user' => $reservation->user,
                ];
            });

        return response()->json($reservations);
    }

    /**
     * Devuelve todas las reservas para las propiedades del propietario autenticado.
     * (Para vista de reservas del propietario)
     */
    public function ownerBookings(Request $request)
    {
        $user = $request->user();

        $reservations = Reservation::whereHas('property', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with(['property', 'user'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($reservation) {
                $details = [];
                if ($reservation->details) {
                    $details = json_decode($reservation->details, true) ?: [];
                }
                return [
                    'id' => $reservation->id,
                    'propertyId' => $reservation->property_id,
                    'userId' => $reservation->user_id,
                    'checkIn' => $details['check_in'] ?? ($reservation->reservation_date . 'T' . $reservation->reservation_time),
                    'checkOut' => $details['check_out'] ?? null,
                    'guests' => $details['guests'] ?? null,
                    'status' => $details['status'] ?? null,
                    'createdAt' => $reservation->created_at,
                    'property' => $reservation->property,
                    'user' => $reservation->user,
                ];
            });

        return response()->json($reservations);
    }

    /**
     * Devuelve todas las reservas (admin, solo para gestión interna).
     */
    public function index()
    {
        $reservations = Reservation::with(['property', 'user'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($reservation) {
                $details = [];
                if ($reservation->details) {
                    $details = json_decode($reservation->details, true) ?: [];
                }
                return [
                    'id' => $reservation->id,
                    'propertyId' => $reservation->property_id,
                    'userId' => $reservation->user_id,
                    'checkIn' => $details['check_in'] ?? ($reservation->reservation_date . 'T' . $reservation->reservation_time),
                    'checkOut' => $details['check_out'] ?? null,
                    'guests' => $details['guests'] ?? null,
                    'status' => $details['status'] ?? null,
                    'createdAt' => $reservation->created_at,
                    'property' => $reservation->property,
                    'user' => $reservation->user,
                ];
            });

        return response()->json($reservations);
    }
}
