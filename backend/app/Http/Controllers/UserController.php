<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // Actualizar datos del perfil del usuario autenticado
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        // Valida los campos que esperas actualizar
        $validated = $request->validate([
            'username' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8',
            // Añade aquí más campos si tu tabla los tiene
        ]);

        // Si el usuario quiere actualizar la contraseña
        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }

        $user->update($validated);

        return response()->json(['user' => $user]);
    }

    // Obtener el perfil del usuario autenticado
    public function profile(Request $request)
    {
        return response()->json(['user' => $request->user()]);
    }
}
