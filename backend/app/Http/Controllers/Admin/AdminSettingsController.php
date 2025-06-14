<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Property;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AdminSettingsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/admin/stats",
     *     summary="Estadísticas del sistema para administradores",
     *     tags={"Admin"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Estadísticas obtenidas correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="total_users", type="integer"),
     *             @OA\Property(property="active_users", type="integer"),
     *             @OA\Property(property="admin_users", type="integer"),
     *             @OA\Property(property="recent_registrations", type="integer"),
     *             @OA\Property(property="total_properties", type="integer"),
     *             @OA\Property(property="total_reservations", type="integer"),
     *         )
     *     ),
     *     @OA\Response(response=403, description="No autorizado"),
     * )
     */
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('verificado', true)->count(), // Cambié is_active por verificado
            'admin_users' => User::where('role', 'admin')->count(), // Cambié la consulta para usar tu estructura
            'recent_registrations' => User::where('fecha_registro', '>=', now()->subDays(7))->count(), // Cambié created_at por fecha_registro
            'total_properties' => Property::count(),
            'total_reservations' => Reservation::count(),
        ];

        return view('admin.settings.index', compact('stats'));
    }

    public function users(Request $request)
    {
        $query = User::query(); // Cambié para usar tu estructura sin roles

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(15)->withQueryString();

        return view('admin.settings.users', compact('users'));
    }

    public function updateUser(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'username' => ['required', 'string', Rule::unique('users')->ignore($user->id)],
            'verificado' => 'boolean',
            'role' => 'required|in:guest,owner,user,admin'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Solo actualizar los campos que se enviaron
        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'verificado' => $request->boolean('verificado', false),
            'role' => $request->role
        ];

        // Solo actualizar username si se envió
        if ($request->filled('username')) {
            $updateData['username'] = $request->username;
        }

        $user->update($updateData);

        return back()->with('success', __('admin.user_updated_successfully'));
    }

    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', __('admin.cannot_delete_yourself'));
        }

        $user->delete();

        return back()->with('success', __('admin.user_deleted_successfully'));
    }

    public function properties(Request $request)
    {
        $query = Property::with('owner'); // Cambiar 'user' por 'owner'

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%") // Cambiar 'title' por 'name'
                    ->orWhere('location', 'like', "%{$search}%"); // Cambiar 'address' por 'location'
            });
        }

        $properties = $query->paginate(10)->withQueryString();

        return view('admin.settings.properties', compact('properties'));
    }

    public function reservations(Request $request)
    {
        $query = Reservation::with(['user', 'property']); // 'user' está bien aquí

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('property', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%"); // Cambiar 'title' por 'name'
            })->orWhereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            // Usar el accessor que definiste en el modelo
            $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(details, '$.status')) = ?", [$request->status]);
        }

        $reservations = $query->paginate(15)->withQueryString();

        return view('admin.settings.reservations', compact('reservations'));
    }
    public function systemSettings()
    {
        $settings = [
            'app_name' => config('app.name'),
            'app_url' => config('app.url'),
            'mail_driver' => config('mail.default'),
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
        ];

        return view('admin.settings.system', compact('settings'));
    }
}
