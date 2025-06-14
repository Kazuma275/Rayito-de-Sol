<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    // Actualizar datos del perfil del usuario autenticado
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

    // Obtener el perfil del usuario autenticado
    public function profile(Request $request)
    {
        return response()->json(['user' => $request->user()]);
    }

    public function destroy(Request $request)
    {
        $user = $request->user();

        // Puedes hacer un soft delete, o eliminar completamente el usuario
        // Si usas SoftDeletes:
        // $user->delete();

        // Si quieres eliminar completamente:
        $user->forceDelete();

        // Cierra la sesión o invalida el token si es necesario

        return response()->json(['message' => 'Cuenta eliminada correctamente']);
    }

    // Método seguro para obtener los detalles como array

    public function dashboardSummary(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Propiedades del usuario
            $properties = $user->properties()->with(['reservations'])->get();
            $totalProperties = $properties->count();

            // Reservas como propietario
            $reservationsAsOwner = $user->propertyReservations()
                ->with(['property:id,name,location,image,user_id'])
                ->get();

            // Reservas como huésped
            $reservationsAsGuest = $user->reservations()
                ->with(['property:id,name,location,image,user_id'])
                ->get();

            // --- INGRESOS TOTALES (como propietario) ---
            // Suma todos los total_price de las reservas de tus propiedades
            $totalRevenue = $reservationsAsOwner->reduce(function ($carry, $r) {
                $details = $this->safeDetails($r->details);
                $price = isset($details['total_price']) ? floatval($details['total_price']) : 0;
                return $carry + ($price > 0 ? $price : 0);
            }, 0);

            // --- INGRESOS DEL MES ACTUAL (como propietario) ---
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

            // --- OCUPACIÓN REAL ---
            // Calcula noches reservadas (aunque haya solapamientos, igual que SQL SUM)
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

            // Noches posibles: nº propiedades * 365
            $totalPossibleNights = $totalProperties * 365;
            $occupancyRate = $totalPossibleNights > 0
                ? min(100, max(0, round(($totalNightsBooked / $totalPossibleNights) * 100)))
                : 0;

            // --- PRÓXIMAS RESERVAS (como propietario, próximas en fecha) ---
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
    public function renterSummary(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $bookings = $user->reservations()
                ->with('property:id,name,location,image')
                ->whereHas('property') // evita reservas huérfanas
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
