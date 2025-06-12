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

        // Obtener todas las propiedades del usuario
        $properties = Property::where('user_id', $user->id)
            ->select('id', 'name')
            ->get();

        // Obtener todas las reservas de esas propiedades
        $reservations = Reservation::whereIn('property_id', $properties->pluck('id'))->get();

        // Total de propiedades
        $totalProperties = $properties->count();

        // Total de reservas
        $totalBookings = $reservations->count();

        // Revenue total (sumando total_price desde details)
        $totalRevenue = $reservations->sum(function($r) {
            $details = json_decode($r->details, true) ?? [];
            return $details['total_price'] ?? 0;
        });

        // Porcentaje de ocupación global (todas las propiedades)
        $totalNights = 0;
        foreach ($reservations as $r) {
            $details = json_decode($r->details, true) ?? [];
            $checkIn = isset($details['check_in']) ? strtotime($details['check_in']) : null;
            $checkOut = isset($details['check_out']) ? strtotime($details['check_out']) : null;
            if ($checkIn && $checkOut && $checkOut > $checkIn) {
                $totalNights += ($checkOut - $checkIn) / (60 * 60 * 24);
            }
        }
        $maxNights = $totalProperties * 365;
        $occupancyRate = $maxNights > 0 ? round(($totalNights / $maxNights) * 100) : 0;

        // Reservas por mes
        $bookingsByMonth = array_fill(1, 12, 0);
        foreach ($reservations as $r) {
            $details = json_decode($r->details, true) ?? [];
            if (isset($details['check_in'])) {
                $month = date('n', strtotime($details['check_in']));
                $bookingsByMonth[$month]++;
            }
        }

        // Revenue y ocupación por propiedad individual
        $revenueByProperty = [];
        foreach ($properties as $property) {
            $revenue = 0;
            $totalNights = 0;
            foreach ($reservations as $r) {
                if ($r->property_id == $property->id) {
                    $details = json_decode($r->details, true) ?? [];
                    $revenue += $details['total_price'] ?? 0;
                    $checkIn = isset($details['check_in']) ? strtotime($details['check_in']) : null;
                    $checkOut = isset($details['check_out']) ? strtotime($details['check_out']) : null;
                    if ($checkIn && $checkOut && $checkOut > $checkIn) {
                        $totalNights += ($checkOut - $checkIn) / (60 * 60 * 24);
                    }
                }
            }
            $occupancy = round(($totalNights / 365) * 100);
            $revenueByProperty[] = [
                'id' => $property->id,
                'name' => $property->name,
                'revenue' => $revenue,
                'occupancy' => $occupancy
            ];
        }

        return response()->json([
            'totalProperties'   => $totalProperties,
            'totalBookings'     => $totalBookings,
            'occupancyRate'     => $occupancyRate,
            'totalRevenue'      => $totalRevenue,
            'bookingsByMonth'   => $bookingsByMonth,
            'revenueByProperty' => $revenueByProperty
        ]);
    }
}
