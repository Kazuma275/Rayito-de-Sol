<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class AuthenticatedSessionController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login de usuario",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login exitoso",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string"),
     *             @OA\Property(property="user", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Credenciales inválidas")
     * )
     */
    public function login(Request $request)
    {
        // Validación
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Buscar usuario
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        // Generar token Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }

    /**
     * Registro de usuario para API.
     */
    public function register(Request $request)
    {
        // Validar entrada
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Crear usuario (ajusta el nombre del campo si es username o name)
            $user = User::create([
                'name' => $request->username,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Crear token (Sanctum)
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Usuario registrado exitosamente',
                'user' => $user,
                'token' => $token,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al registrar el usuario. Por favor, inténtalo de nuevo.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Enviar email de recuperación de contraseña
     */
    /**
     * Enviar email de recuperación de contraseña
     */
    public function forgotPassword(Request $request)
    {
        try {
            Log::info('=== INICIO forgotPassword ===', ['email' => $request->email]);

            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
            ], [
                'email.exists' => 'No encontramos una cuenta con este correo electrónico',
                'email.required' => 'El correo electrónico es requerido',
                'email.email' => 'Por favor, introduce un correo electrónico válido',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors()->first()
                ], 404);
            }

            $user = User::where('email', $request->email)->first();
            Log::info('Usuario encontrado', ['user_id' => $user->id, 'email' => $user->email]);

            // Generar token de recuperación
            $token = Str::random(64);
            Log::info('Token generado', ['token' => $token]);

            // Verificar campos antes de actualizar
            Log::info('Campos del usuario antes de actualizar', [
                'fillable' => $user->getFillable(),
                'attributes' => array_keys($user->getAttributes())
            ]);

            // Guardar token en la base de datos
            try {
                $user->reset_password_token = $token;
                $user->reset_password_expires_at = Carbon::now()->addHours(24);
                $saved = $user->save();

                Log::info('Token guardado', [
                    'saved' => $saved,
                    'user_id' => $user->id,
                    'token_in_db' => $user->reset_password_token,
                    'expires_at' => $user->reset_password_expires_at
                ]);

                // Verificar que se guardó correctamente
                $userCheck = User::find($user->id);
                Log::info('Verificación después de guardar', [
                    'token_in_db' => $userCheck->reset_password_token,
                    'expires_at' => $userCheck->reset_password_expires_at
                ]);
            } catch (\Exception $e) {
                Log::error('Error guardando token: ' . $e->getMessage());
                return response()->json([
                    'message' => 'Error guardando token: ' . $e->getMessage()
                ], 500);
            }

            // Crear enlace de recuperación
            $resetUrl = env('FRONTEND_URL') . '/portal/reset-password?token=' . $token;
            Log::info('URL creada', ['resetUrl' => $resetUrl]);

            // Enviar email
            Mail::send('emails.password-reset', [
                'user' => $user,
                'resetUrl' => $resetUrl,
                'token' => $token
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                    ->subject('Recuperación de contraseña - Rayito de Sol');
            });

            Log::info('Email enviado exitosamente');

            return response()->json([
                'message' => 'Correo de recuperación enviado exitosamente',
                'debug_token' => $token // Solo para debugging, quitar en producción
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error en forgotPassword: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Error al enviar el correo. Inténtalo de nuevo.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar si el token de recuperación es válido
     */
    /**
     * Verificar si el token de recuperación es válido
     */
    public function verifyResetToken(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Token requerido'
                ], 400);
            }

            Log::info('Verificando token', [
                'token' => substr($request->token, 0, 10) . '...',
                'current_time' => Carbon::now()->toDateTimeString()
            ]);

            $user = User::where('reset_password_token', $request->token)->first();

            if (!$user) {
                Log::error('Token no encontrado en BD', ['token' => substr($request->token, 0, 10) . '...']);
                return response()->json([
                    'message' => 'Token inválido'
                ], 400);
            }

            Log::info('Usuario encontrado', [
                'user_id' => $user->id,
                'token_expires_at' => $user->reset_password_expires_at ? $user->reset_password_expires_at->toDateTimeString() : 'null',
                'current_time' => Carbon::now()->toDateTimeString(),
                'is_expired' => $user->reset_password_expires_at ? $user->reset_password_expires_at->isPast() : 'no_expiry_set'
            ]);

            if (!$user->reset_password_expires_at || $user->reset_password_expires_at->isPast()) {
                Log::error('Token expirado', [
                    'expires_at' => $user->reset_password_expires_at ? $user->reset_password_expires_at->toDateTimeString() : 'null',
                    'current_time' => Carbon::now()->toDateTimeString()
                ]);
                return response()->json([
                    'message' => 'Token expirado'
                ], 400);
            }

            return response()->json([
                'message' => 'Token válido',
                'expires_at' => $user->reset_password_expires_at->toDateTimeString()
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error en verifyResetToken: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Restablecer contraseña
     */
    public function resetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required|string',
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).*$/',
                    'confirmed'
                ],
            ], [
                'password.regex' => 'La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula y un número',
                'password.confirmed' => 'Las contraseñas no coinciden',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors()->first()
                ], 400);
            }

            // Buscar usuario con token válido
            $user = User::where('reset_password_token', $request->token)
                ->where('reset_password_expires_at', '>', Carbon::now())
                ->first();

            if (!$user) {
                return response()->json([
                    'message' => 'Token inválido o expirado'
                ], 400);
            }

            // Actualizar contraseña y limpiar tokens
            $user->update([
                'password' => Hash::make($request->password),
                'reset_password_token' => null,
                'reset_password_expires_at' => null,
            ]);

            // Enviar email de confirmación
            try {
                Mail::send('emails.password-reset-confirmation', [
                    'user' => $user
                ], function ($message) use ($user) {
                    $message->to($user->email, $user->name)
                        ->subject('Contraseña restablecida - Rayito de Sol');
                });
            } catch (\Exception $e) {
                Log::error('Error enviando email de confirmación: ' . $e->getMessage());
            }

            return response()->json([
                'message' => 'Contraseña restablecida exitosamente'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error en resetPassword: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }
}
