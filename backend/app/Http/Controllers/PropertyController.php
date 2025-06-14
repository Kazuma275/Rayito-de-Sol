<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use Illuminate\Support\Facades\Log;

class PropertyController extends Controller
{

    public function update(Request $request, $id)
    {
        $property = Property::findOrFail($id);

        // Valida los campos
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'bedrooms' => 'required|integer|min:1',
            'capacity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'image' => 'required|url',
            'description' => 'required|string',
            'amenities' => 'nullable|string', // podrías guardar como string separado por comas
        ]);

        // Actualiza la propiedad
        $property->update($validated);

        return response()->json([
            'message' => 'Propiedad actualizada correctamente',
            'property' => $property
        ]);
    }

    public function destroy($id)
    {
        $property = Property::findOrFail($id);
        $property->delete();

        return response()->json(['message' => 'Propiedad eliminada correctamente']);
    }
    /**
     * Store a newly created property in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
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

    public function show($id)
    {
        // ✅ También aquí cargamos las reviews
        $property = Property::with('reviews')->find($id);
        if (!$property) {
            return response()->json(['message' => 'Propiedad no encontrada'], 404);
        }
        return response()->json($property);
    }

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

        $properties = $query->with('reviews')->get(); // ✅ Cargamos las reviews también en búsqueda

        return response()->json($properties);
    }
}
