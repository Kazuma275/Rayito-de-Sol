<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;

class PropertyController extends Controller
{
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

        // Agrega el user_id del usuario autenticado
        $data['user_id'] = auth()->id();

        $property = Property::create($data);
        return response()->json($property, 201);
    }

    public function index()
    {
        return response()->json(Property::all());
    }

        public function show($id)
    {
        $property = Property::find($id);
        if (!$property) {
            return response()->json(['message' => 'Propiedad no encontrada'], 404);
        }
        return response()->json($property);
    }
}
