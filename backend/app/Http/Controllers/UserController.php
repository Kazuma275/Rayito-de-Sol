<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * @OA\Put(
     *     path="/api/profile",
     *     summary="Actualizar datos del perfil del usuario autenticado",
     *     tags={"Perfil"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=false,
     *         description="Datos para actualizar el perfil",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", maxLength=255, example="Juan Pérez"),
     *             @OA\Property(property="email", type="string", format="email", maxLength=255, example="juan@example.com"),
     *             @OA\Property(property="phone", type="string", maxLength=25, example="34123456789")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Perfil actualizado correctamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Perfil actualizado correctamente"),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 description="Usuario actualizado",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Juan Pérez"),
     *                 @OA\Property(property="email", type="string", format="email", example="juan@example.com"),
     *                 @OA\Property(property="phone", type="string", example="+34123456789")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
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
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:25',
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Perfil actualizado correctamente',
            'user' => $user,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/profile",
     *     summary="Obtener el perfil del usuario autenticado",
     *     tags={"Perfil"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Perfil del usuario autenticado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Juan Pérez"),
     *                 @OA\Property(property="email", type="string", format="email", example="juan@example.com"),
     *                 @OA\Property(property="phone", type="string", example="+34123456789")
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
    public function profile(Request $request)
    {
        return response()->json(['user' => $request->user()]);
    }

    /**
     * @OA\Delete(
     *     path="/api/profile",
     *     summary="Eliminar la cuenta del usuario autenticado",
     *     tags={"Perfil"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Cuenta eliminada correctamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Cuenta eliminada correctamente")
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
    public function destroy(Request $request)
    {
        $user = $request->user();
        $user->forceDelete();
        return response()->json(['message' => 'Cuenta eliminada correctamente']);
    }

    /**
     * @OA\Get(
     *     path="/api/dashboard-summary",
     *     summary="Obtener resumen del panel del usuario",
     *     tags={"Perfil"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Resumen del dashboard del usuario",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Juan Pérez"),
     *             @OA\Property(property="email", type="string", example="juan@example.com"),
     *             @OA\Property(property="lastLogin", type="string", example="2024-01-01 12:00:00"),
     *             @OA\Property(property="totalProperties", type="integer", example=5),
     *             @OA\Property(property="reservationsAsOwner", type="integer", example=10),
     *             @OA\Property(property="reservationsAsGuest", type="integer", example=3),
     *             @OA\Property(property="monthlyRevenue", type="number", format="float", example=1200.50),
     *             @OA\Property(property="totalRevenue", type="number", format="float", example=35000.75),
     *             @OA\Property(property="occupancyRate", type="number", format="float", example=75.2),
     *             @OA\Property(
     *                 property="upcomingBookings",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="details", type="object"),
     *                     @OA\Property(property="check_in", type="string", example="2024-07-01"),
     *                     @OA\Property(property="check_out", type="string", example="2024-07-10"),
     *                     @OA\Property(
     *                         property="property",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=2),
     *                         @OA\Property(property="name", type="string", example="Apartamento Centro"),
     *                         @OA\Property(property="location", type="string", example="Madrid"),
     *                         @OA\Property(property="image", type="string", example="image.jpg")
     *                     ),
     *                     @OA\Property(property="status", type="string", example="confirmed")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="guestBookings",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="details", type="object"),
     *                     @OA\Property(property="check_in", type="string", example="2024-07-01"),
     *                     @OA\Property(property="check_out", type="string", example="2024-07-10"),
     *                     @OA\Property(
     *                         property="property",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=2),
     *                         @OA\Property(property="name", type="string", example="Apartamento Centro"),
     *                         @OA\Property(property="location", type="string", example="Madrid"),
     *                         @OA\Property(property="image", type="string", example="image.jpg")
     *                     ),
     *                     @OA\Property(property="status", type="string", example="pending")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Internal Server Error")
     *         )
     *     )
     * )
     */
    public function dashboardSummary(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $properties = $user->properties()->with(['reservations'])->get();
            $totalProperties = $properties->count();

            $reservationsAsOwner = $user->propertyReservations()
                ->with(['property:id,name,location,image,user_id'])
                ->get();

            $reservationsAsGuest = $user->reservations()
                ->with(['property:id,name,location,image,user_id'])
                ->get();

            $totalRevenue = $reservationsAsOwner->reduce(function ($carry, $r) {
                $details = $this->safeDetails($r->details);
                $price = isset($details['total_price']) ? floatval($details['total_price']) : 0;
                return $carry + ($price > 0 ? $price : 0);
            }, 0);

            $monthlyRevenue = $reservationsAsOwner->filter(function ($r) {
                $details = $this->safeDetails($r->details);
                if (isset($details['check_in'])) {
                    $checkIn = \Carbon\Carbon::make($details['check_in']);
                    return $checkIn && $checkIn->isCurrentMonth();
                }
                return false;
            })->reduce(function ($carry, $r) {
                $details = $this->safeDetails($r->details);
                return $carry + (isset($details['total_price']) ? floatval($details['total_price']) : 0);
            }, 0);

            $totalNightsBooked = $reservationsAsOwner->reduce(function ($carry, $r) {
                $details = $this->safeDetails($r->details);
                if (!empty($details['check_in']) && !empty($details['check_out'])) {
                    $checkIn = \Carbon\Carbon::make($details['check_in']);
                    $checkOut = \Carbon\Carbon::make($details['check_out']);
                    if ($checkIn && $checkOut && $checkOut > $checkIn) {
                        $nights = $checkIn->diffInDays($checkOut);
                        return $carry + $nights;
                    }
                }
                return $carry;
            }, 0);

            $totalPossibleNights = $totalProperties * 365;
            $occupancyRate = $totalPossibleNights > 0
                ? min(100, max(0, round(($totalNightsBooked / $totalPossibleNights) * 100)))
                : 0;

            $upcomingBookings = $reservationsAsOwner->filter(function ($r) {
                $details = $this->safeDetails($r->details);
                if (isset($details['check_in'])) {
                    $checkIn = \Carbon\Carbon::make($details['check_in']);
                    return $checkIn && $checkIn->isFuture();
                }
                return false;
            })->sortBy(function ($r) {
                $details = $this->safeDetails($r->details);
                return isset($details['check_in']) ? $details['check_in'] : null;
            })->take(3)->values();

            return response()->json([
                'name' => $user->name,
                'email' => $user->email,
                'lastLogin' => $user->last_login_at,
                'totalProperties' => $totalProperties,
                'reservationsAsOwner' => $reservationsAsOwner->count(),
                'reservationsAsGuest' => $reservationsAsGuest->count(),
                'monthlyRevenue'    => $monthlyRevenue,
                'totalRevenue'      => $totalRevenue,
                'occupancyRate'     => $occupancyRate,
                'upcomingBookings'  => $upcomingBookings->map(function ($booking) {
                    $details = $this->safeDetails($booking->details);
                    $property = $booking->property;
                    return [
                        'id' => $booking->id,
                        'details' => $details,
                        'check_in' => isset($details['check_in']) ? \Carbon\Carbon::make($details['check_in'])->format('Y-m-d') : null,
                        'check_out' => isset($details['check_out']) ? \Carbon\Carbon::make($details['check_out'])->format('Y-m-d') : null,
                        'property' => $property ? [
                            'id' => $property->id,
                            'name' => $property->name,
                            'location' => $property->location,
                            'image' => $property->image,
                        ] : null,
                        'status' => $details['status'] ?? 'pending',
                    ];
                })->values(),
                'guestBookings' => $reservationsAsGuest->map(function ($booking) {
                    $details = $this->safeDetails($booking->details);
                    $property = $booking->property;
                    return [
                        'id' => $booking->id,
                        'details' => $details,
                        'check_in' => isset($details['check_in']) ? \Carbon\Carbon::make($details['check_in'])->format('Y-m-d') : null,
                        'check_out' => isset($details['check_out']) ? \Carbon\Carbon::make($details['check_out'])->format('Y-m-d') : null,
                        'property' => $property ? [
                            'id' => $property->id,
                            'name' => $property->name,
                            'location' => $property->location,
                            'image' => $property->image,
                        ] : null,
                        'status' => $details['status'] ?? 'pending',
                    ];
                })->values(),
            ]);
        } catch (\Throwable $e) {
            \Log::error('Dashboard Summary Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/renter-summary",
     *     summary="Obtener resumen como huésped",
     *     tags={"Perfil"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Resumen de reservas como huésped",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Juan Pérez"),
     *             @OA\Property(property="totalBookings", type="integer", example=7),
     *             @OA\Property(property="citiesVisited", type="integer", example=3),
     *             @OA\Property(property="totalSpent", type="number", format="float", example=2100.00)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Internal Server Error")
     *         )
     *     )
     * )
     */
    public function renterSummary(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $bookings = $user->reservations()
                ->with('property:id,name,location,image')
                ->whereHas('property')
                ->get();

            $totalBookings = $bookings->count();

            $citiesVisited = $bookings
                ->pluck('property.location')
                ->filter()
                ->unique()
                ->count();

            $totalSpent = $bookings->reduce(function ($carry, $booking) {
                $details = is_array($booking->details)
                    ? $booking->details
                    : (is_string($booking->details) ? json_decode($booking->details, true) : []);

                return $carry + (floatval($details['total_price'] ?? 0));
            }, 0);

            return response()->json([
                'name' => $user->name,
                'totalBookings' => $totalBookings,
                'citiesVisited' => $citiesVisited,
                'totalSpent' => $totalSpent,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Renter Summary Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Decodifica el campo details de una reserva de forma segura (array o null).
     */
    protected function safeDetails($details)
    {
        if (is_array($details)) return $details;
        if (is_string($details)) {
            $decoded = json_decode($details, true);
            return is_array($decoded) ? $decoded : [];
        }
        return [];
    }

    /**
     * @OA\Post(
     *     path="/api/profile/change-password",
     *     summary="Cambiar la contraseña del usuario autenticado",
     *     tags={"Perfil"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"current_password", "new_password"},
     *             @OA\Property(property="current_password", type="string", example="passwordantigua"),
     *             @OA\Property(property="new_password", type="string", minLength=8, example="nuevacontraseña123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Contraseña cambiada correctamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Contraseña cambiada correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="La contraseña actual no es correcta",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="La contraseña actual no es correcta")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
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
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8'
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'La contraseña actual no es correcta'], 400);
        }

        $user->password = bcrypt($request->new_password);
        $user->save();

        return response()->json(['message' => 'Contraseña cambiada correctamente']);
    }
}
