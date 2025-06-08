<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

    // NUEVO: Resumen para el dashboard del usuario autenticado
    public function dashboardSummary(Request $request)
    {
    $user = $request->user();
    $reservations = $user->propertyReservations;

    // Total de propiedades
    $totalProperties = $user->properties()->count();

    // Filtrar reservas activas desde details
    $activeBookings = $reservations->filter(function($r) {
        $details = $r->details ?? [];
        return ($details['status'] ?? 'pending') === 'active';
    });

    // Ingresos del mes actual desde details
    $monthlyRevenue = $reservations->filter(function($r) {
        $details = $r->details ?? [];
        if (isset($details['check_in'])) {
            $checkIn = \Carbon\Carbon::parse($details['check_in']);
            return $checkIn->isCurrentMonth();
        }
        return false;
    })->sum(function($r) {
        $details = $r->details ?? [];
        return $details['total_price'] ?? 0;
    });

    // Tasa de ocupación
    $occupancyRate = $totalProperties > 0 ? round(($activeBookings->count() / max(1, $totalProperties)) * 100) : 0;

    return response()->json([
        'name' => $user->name,
        'email' => $user->email,
        'lastLogin' => $user->last_login_at,
        'totalProperties' => $totalProperties,
        'totalBookings' => $reservations->count(),
        'monthlyRevenue' => $monthlyRevenue,
        'occupancyRate' => $occupancyRate,
        'upcomingBookings' => $reservations->filter(function($r) {
            $details = $r->details ?? [];
            // Asume que check_in está en details y es una fecha futura
            if (isset($details['check_in'])) {
                return \Carbon\Carbon::parse($details['check_in'])->isFuture();
            }
            return false;
        })->take(3)->values(),
        // 'recentMessages' => ...
    ]);
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
