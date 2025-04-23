<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email', // Asegurarse de que el correo sea único
            'password' => 'required|string|min:8|confirmed', // Validación de la contraseña y su confirmación
        ]);

        // Si la validación falla, retornar los errores
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
            ], 422); // 422 Unprocessable Entity
        }

        // Crear un nuevo usuario
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password), // Asegurarse de encriptar la contraseña
            ]);

            // Aquí puedes generar un token si estás usando Sanctum o JWT
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
}
