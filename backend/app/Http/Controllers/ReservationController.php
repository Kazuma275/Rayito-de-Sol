<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;

class ReservationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'reservation_date' => 'required|date',
            'reservation_time' => 'required',
            'details' => 'nullable|string',
        ]);

        $reservation = Reservation::create($validated);

        return response()->json([
            'success' => true,
            'reservation' => $reservation,
        ], 201);
    }
}
