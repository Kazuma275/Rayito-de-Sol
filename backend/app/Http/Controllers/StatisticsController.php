<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Property;
use App\Models\Reservation;

class StatisticsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/statistics",
     *     summary="Obtener estadísticas generales del usuario propietario",
     *     tags={"Estadísticas"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Estadísticas generales",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="totalProperties", type="integer", example=3),
     *             @OA\Property(property="totalBookings", type="integer", example=25),
     *             @OA\Property(property="occupancyRate", type="number", example=68),
     *             @OA\Property(property="totalRevenue", type="number", format="float", example=15300.50),
     *             @OA\Property(
     *                 property="bookingsByMonth",
     *                 type="object",
     *                 @OA\Property(property="1", type="integer", example=3),
     *                 @OA\Property(property="2", type="integer", example=2),
     *                 @OA\Property(property="3", type="integer", example=4),
     *                 @OA\Property(property="4", type="integer", example=1),
     *                 @OA\Property(property="5", type="integer", example=3),
     *                 @OA\Property(property="6", type="integer", example=2),
     *                 @OA\Property(property="7", type="integer", example=1),
     *                 @OA\Property(property="8", type="integer", example=2),
     *                 @OA\Property(property="9", type="integer", example=2),
     *                 @OA\Property(property="10", type="integer", example=1),
     *                 @OA\Property(property="11", type="integer", example=2),
     *                 @OA\Property(property="12", type="integer", example=2)
     *             ),
     *             @OA\Property(
     *                 property="revenueByProperty",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Apartamento Centro"),
     *                     @OA\Property(property="revenue", type="number", format="float", example=5400.00),
     *                     @OA\Property(property="occupancy", type="integer", example=74)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function statistics(Request $request)
    {
        $user = Auth::user();

        $properties = Property::where('user_id', $user->id)
            ->select('id', 'name')
            ->get();

        $reservations = Reservation::whereIn('property_id', $properties->pluck('id'))->get();

        $totalProperties = $properties->count();
        $totalBookings = $reservations->count();

        $totalRevenue = $reservations->sum(function($r) {
            $details = json_decode($r->details, true) ?? [];
            return $details['total_price'] ?? 0;
        });

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

        $bookingsByMonth = array_fill(1, 12, 0);
        foreach ($reservations as $r) {
            $details = json_decode($r->details, true) ?? [];
            if (isset($details['check_in'])) {
                $month = date('n', strtotime($details['check_in']));
                $bookingsByMonth[$month]++;
            }
        }

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
