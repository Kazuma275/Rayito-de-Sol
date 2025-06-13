<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use Illuminate\Support\Facades\Log;

class PropertyController extends Controller
{
    /**
     * @OA\Put(
     *     path="/api/properties/{id}",
     *     summary="Actualizar una propiedad",
     *     tags={"Propiedades"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la propiedad",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","location","bedrooms","capacity","price","status","image","description"},
     *             @OA\Property(property="name", type="string", example="Apartamento Centro"),
     *             @OA\Property(property="location", type="string", example="Madrid"),
     *             @OA\Property(property="bedrooms", type="integer", example=2),
     *             @OA\Property(property="capacity", type="integer", example=4),
     *             @OA\Property(property="price", type="number", format="float", example=120.00),
     *             @OA\Property(property="status", type="string", enum={"active","inactive"}, example="active"),
     *             @OA\Property(property="image", type="string", example="https://example.com/image.jpg"),
     *             @OA\Property(property="description", type="string", example="Bonito apartamento en el centro"),
     *             @OA\Property(property="amenities", type="string", example="wifi,parking,piscina")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Propiedad actualizada correctamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Propiedad actualizada correctamente"),
     *             @OA\Property(
     *                 property="property",
     *                 type="object"
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
     *         response=404,
     *         description="Propiedad no encontrada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Propiedad no encontrada")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $property = Property::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'bedrooms' => 'required|integer|min:1',
            'capacity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'image' => 'required|url',
            'description' => 'required|string',
            'amenities' => 'nullable|string',
        ]);

        $property->update($validated);

        return response()->json([
            'message' => 'Propiedad actualizada correctamente',
            'property' => $property
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/properties/{id}",
     *     summary="Eliminar una propiedad",
     *     tags={"Propiedades"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la propiedad",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Propiedad eliminada correctamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Propiedad eliminada correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Propiedad no encontrada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Propiedad no encontrada")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $property = Property::findOrFail($id);
        $property->delete();

        return response()->json(['message' => 'Propiedad eliminada correctamente']);
    }

    /**
     * @OA\Post(
     *     path="/api/properties",
     *     summary="Crear una nueva propiedad",
     *     tags={"Propiedades"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","location","bedrooms","capacity","price","description","status"},
     *             @OA\Property(property="name", type="string", example="Apartamento Centro"),
     *             @OA\Property(property="location", type="string", example="Madrid"),
     *             @OA\Property(property="bedrooms", type="integer", example=2),
     *             @OA\Property(property="capacity", type="integer", example=4),
     *             @OA\Property(property="price", type="number", format="float", example=120.00),
     *             @OA\Property(property="image", type="string", example="https://example.com/image.jpg"),
     *             @OA\Property(property="description", type="string", example="Bonito apartamento en el centro"),
     *             @OA\Property(property="amenities", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="status", type="string", enum={"active","inactive"}, example="active")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Propiedad creada correctamente",
     *         @OA\JsonContent(
     *             type="object"
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
     *     )
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'location' => 'required|string',
            'bedrooms' => 'required|integer',
            'capacity' => 'required|integer',
            'price' => 'required|numeric',
            'image' => 'nullable|string',
            'description' => 'required|string',
            'amenities' => 'nullable|array',
            'status' => 'required|in:active,inactive',
        ]);

        $data['user_id'] = auth()->id();

        $property = Property::create($data);
        return response()->json($property, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/properties",
     *     summary="Listar propiedades del usuario autenticado",
     *     tags={"Propiedades"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de propiedades",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json(
            Property::with('reviews')->where('user_id', $user->id)->get()
        );
    }

    /**
     * @OA\Get(
     *     path="/api/properties/{id}",
     *     summary="Obtener los detalles de una propiedad",
     *     tags={"Propiedades"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la propiedad",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles de la propiedad",
     *         @OA\JsonContent(
     *             type="object"
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Propiedad no encontrada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Propiedad no encontrada")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $property = Property::with('reviews')->find($id);
        if (!$property) {
            return response()->json(['message' => 'Propiedad no encontrada'], 404);
        }
        return response()->json($property);
    }

    /**
     * @OA\Get(
     *     path="/api/properties/search",
     *     summary="Buscar propiedades disponibles",
     *     tags={"Propiedades"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=false,
     *         description="Texto de búsqueda para nombre, ubicación o tipo",
     *         @OA\Schema(type="string", example="Madrid")
     *     ),
     *     @OA\Parameter(
     *         name="guests",
     *         in="query",
     *         required=false,
     *         description="Capacidad mínima",
     *         @OA\Schema(type="integer", example=4)
     *     ),
     *     @OA\Parameter(
     *         name="maxPrice",
     *         in="query",
     *         required=false,
     *         description="Precio máximo",
     *         @OA\Schema(type="number", format="float", example=150.00)
     *     ),
     *     @OA\Parameter(
     *         name="propertyTypes",
     *         in="query",
     *         required=false,
     *         description="Tipos de propiedad (separados por comas)",
     *         @OA\Schema(type="string", example="apartamento,casa")
     *     ),
     *     @OA\Parameter(
     *         name="bedrooms",
     *         in="query",
     *         required=false,
     *         description="Número mínimo de habitaciones",
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *     @OA\Parameter(
     *         name="bathrooms",
     *         in="query",
     *         required=false,
     *         description="Número mínimo de baños",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="amenities",
     *         in="query",
     *         required=false,
     *         description="Comodidades requeridas (separadas por comas)",
     *         @OA\Schema(type="string", example="wifi,parking")
     *     ),
     *     @OA\Parameter(
     *         name="checkIn",
     *         in="query",
     *         required=false,
     *         description="Fecha de check-in (YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date", example="2024-07-01")
     *     ),
     *     @OA\Parameter(
     *         name="checkOut",
     *         in="query",
     *         required=false,
     *         description="Fecha de check-out (YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date", example="2024-07-10")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Resultados de la búsqueda",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(type="object")
     *         )
     *     )
     * )
     */
    public function search(Request $request)
    {
        \Log::info('Entrando al método search');
        $query = Property::query()->where('status', 'active');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('location', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%");
            });
        }

        if ($request->filled('guests')) {
            $query->where('capacity', '>=', $request->input('guests'));
        }

        if ($request->filled('maxPrice')) {
            $query->where('price', '<=', $request->input('maxPrice'));
        }

        if ($request->filled('propertyTypes')) {
            $types = $request->input('propertyTypes');
            if (is_string($types)) {
                $types = array_filter(explode(',', $types));
            }
            if ($types) {
                $query->whereIn('type', $types);
            }
        }

        if ($request->filled('bedrooms')) {
            $query->where('bedrooms', '>=', $request->input('bedrooms'));
        }

        if ($request->filled('bathrooms')) {
            $query->where('bathrooms', '>=', $request->input('bathrooms'));
        }

        if ($request->filled('amenities')) {
            $amenities = $request->input('amenities');
            if (is_string($amenities)) {
                $amenities = array_filter(explode(',', $amenities));
            }
            if ($amenities) {
                foreach ($amenities as $amenity) {
                    $query->whereJsonContains('amenities', $amenity);
                }
            }
        }

        if ($request->filled('checkIn') && $request->filled('checkOut')) {
            $checkIn = $request->input('checkIn');
            $checkOut = $request->input('checkOut');

            $query->whereDoesntHave('reservations', function ($q) use ($checkIn, $checkOut) {
                $q->where('status', '!=', 'cancelled')
                    ->where(function ($r) use ($checkIn, $checkOut) {
                        $r->where('check_in', '<', $checkOut)
                            ->where('check_out', '>', $checkIn);
                    });
            });

            $query->whereDoesntHave('unavailableDates', function ($q) use ($checkIn, $checkOut) {
                $q->where(function ($d) use ($checkIn, $checkOut) {
                    $d->where('start_date', '<', $checkOut)
                        ->where('end_date', '>', $checkIn);
                });
            });
        }

        $properties = $query->with('reviews')->get();

        return response()->json($properties);
    }
}
