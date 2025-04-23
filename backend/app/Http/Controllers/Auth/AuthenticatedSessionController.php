<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthenticatedSessionController extends Controller
{
    /**
     * Iniciar sesión el usuario.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
        ])) {
            $user = Auth::user();

            // Aquí puedes devolver el token o el resultado de la autenticación
            return response()->json([
                'message' => 'Inicio de sesión exitoso',
                'user' => $user,
                // Si estás utilizando JWT o cualquier tipo de token:
                // 'token' => $user->createToken('YourAppName')->plainTextToken,
            ]);
        }

        return response()->json(['message' => 'Credenciales incorrectas'], 401);
    }
}
