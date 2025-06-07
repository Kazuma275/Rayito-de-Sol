<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Property;
use App\Models\Reservation;

class StatisticsController extends Controller
{
    public function statistics(Request $request)
    {
        $user = Auth::user();

        // Get all properties belonging to the user
        $properties = Property::where('user_id', $user->id)
            ->select('id', 'name')
            ->get();

        // Get all reservations for the user's properties
        $reservations = Reservation::whereIn('property_id', $properties->pluck('id'))->get();

        // Total properties
        $totalProperties = $properties->count();

        // Total bookings
        $totalBookings = $reservations->count();

        // Total revenue
        $totalRevenue = $reservations->sum(function($r) {
            return $r->details['total_price'] ?? 0;
        });

        // Occupancy rate (percentage of occupied nights over all possible nights in a year)
        $totalNights = 0;
        foreach ($reservations as $r) {
            $checkIn = isset($r->details['check_in']) ? strtotime($r->details['check_in']) : null;
            $checkOut = isset($r->details['check_out']) ? strtotime($r->details['check_out']) : null;
            if ($checkIn && $checkOut && $checkOut > $checkIn) {
                $totalNights += ($checkOut - $checkIn) / (60 * 60 * 24);
            }
        }
        $maxNights = $totalProperties * 365;
        $occupancyRate = $maxNights > 0 ? round(($totalNights / $maxNights) * 100) : 0;

        // Bookings per month
        $bookingsByMonth = array_fill(1, 12, 0);
        foreach ($reservations as $r) {
            if (isset($r->details['check_in'])) {
                $month = date('n', strtotime($r->details['check_in']));
                $bookingsByMonth[$month]++;
            }
        }

        // Revenue by property
        $revenueByProperty = [];
        foreach ($properties as $property) {
            $revenue = 0;
            foreach ($reservations as $r) {
                if ($r->property_id == $property->id) {
                    $revenue += $r->details['total_price'] ?? 0;
                }
            }
            $revenueByProperty[] = [
                'id' => $property->id,
                'name' => $property->name,
                'revenue' => $revenue
            ];
        }

        return response()->json([
            'totalProperties' => $totalProperties,
            'totalBookings' => $totalBookings,
            'occupancyRate' => $occupancyRate,
            'totalRevenue' => $totalRevenue,
            'bookingsByMonth' => $bookingsByMonth, // Example: [1=>2,2=>0,3=>1,...]
            'revenueByProperty' => $revenueByProperty // Example: [['id'=>1,'name'=>'Property','revenue'=>0], ...]
        ]);
    }
}
