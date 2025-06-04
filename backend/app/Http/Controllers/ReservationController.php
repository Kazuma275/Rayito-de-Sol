<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;

class ReservationController extends Controller
{
    public function propertyBookings($id)
    {
        return response()->json(
            \App\Models\Reservation::where('property_id', $id)->get()
        );
    }
}
