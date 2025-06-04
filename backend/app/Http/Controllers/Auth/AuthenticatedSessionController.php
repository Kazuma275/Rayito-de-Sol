<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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
                // 'token' => $user->createToken('YourAppName')->plainTextToken,
            ]);
        }

        return response()->json(['message' => 'Credenciales incorrectas'], 401);
    }

    /**
     * Registrar un nuevo usuario.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        // Validar la entrada
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255', // Cambiado de 'name' a 'username'
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed', // Se asume que 'password_confirmation' está presente en el formulario
        ]);

        // Si la validación falla, retornar los errores
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
            ], 422); // 422 Unprocessable Entity
        }

        try {
            // Crear un nuevo usuario
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password), // Asegurarse de encriptar la contraseña
            ]);

            // Puedes crear un token si usas Sanctum o JWT aquí
            // $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Usuario registrado exitosamente',
                'user' => $user,
                // 'token' => $token,
            ], 201); // 201 Created
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al registrar el usuario. Por favor, inténtalo de nuevo.',
                'error' => $e->getMessage(),
            ], 500); // 500 Internal Server Error
        }
    }
    public function login(Request $request)
{
    // Validar entrada
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Error de validación',
            'errors' => $validator->errors(),
        ], 422);
    }

    // Buscar usuario
    $user = User::where('email', $request->email)->first();

    // Verificar credenciales
    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Credenciales incorrectas'], 401);
    }

    // Crear token (requiere Laravel Sanctum o Passport)
    $token = $user->createToken('auth_token')->plainTextToken;

    // Devolver el usuario y el token
    return response()->json([
        'message' => 'Inicio de sesión exitoso',
        'user' => $user,
        'token' => $token,
    ]);
}
}
