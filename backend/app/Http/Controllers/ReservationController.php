<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;

class ReservationController extends Controller
{
    public function propertyBookings($id)
    {
        $reservations = Reservation::where('property_id', $id)
            ->with(['property', 'user'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($reservation) {
                $details = $reservation->details ?? [];
                return [
                    'id' => $reservation->id,
                    'propertyId' => $reservation->property_id,
                    'userId' => $reservation->user_id,
                    'checkIn' => $details['check_in'] ?? ($reservation->reservation_date . 'T' . $reservation->reservation_time),
                    'checkOut' => $details['check_out'] ?? null,
                    'guests' => $details['guests'] ?? null,
                    'status' => $details['status'] ?? 'pending',
                    'createdAt' => $reservation->created_at,
                    'property' => $reservation->property,
                    'user' => $reservation->user,
                ];
            });

        return response()->json($reservations);
    }

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
                $details = $reservation->details ?? [];
                return [
                    'id' => $reservation->id,
                    'propertyId' => $reservation->property_id,
                    'userId' => $reservation->user_id,
                    'checkIn' => $details['check_in'] ?? ($reservation->reservation_date . 'T' . $reservation->reservation_time),
                    'checkOut' => $details['check_out'] ?? null,
                    'guests' => $details['guests'] ?? null,
                    'status' => $details['status'] ?? 'pending',
                    'createdAt' => $reservation->created_at,
                    'property' => $reservation->property,
                    'user' => $reservation->user,
                ];
            });

        return response()->json($reservations);
    }

    public function index()
    {
        $reservations = Reservation::with(['property', 'user'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($reservation) {
                $details = $reservation->details ?? [];
                return [
                    'id' => $reservation->id,
                    'propertyId' => $reservation->property_id,
                    'userId' => $reservation->user_id,
                    'checkIn' => $details['check_in'] ?? ($reservation->reservation_date . 'T' . $reservation->reservation_time),
                    'checkOut' => $details['check_out'] ?? null,
                    'guests' => $details['guests'] ?? null,
                    'status' => $details['status'] ?? 'pending',
                    'createdAt' => $reservation->created_at,
                    'property' => $reservation->property,
                    'user' => $reservation->user,
                ];
            });

        return response()->json($reservations);
    }
}
