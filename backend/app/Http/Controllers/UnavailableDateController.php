<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UnavailableDate;

class UnavailableDateController extends Controller
{
    // Obtener todos los días no disponibles para una propiedad
    public function index($propertyId)
    {
        $dates = UnavailableDate::where('property_id', $propertyId)->pluck('date');
        return response()->json($dates);
    }

    // Agregar un día no disponible
    public function store(Request $request, $propertyId)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $date = UnavailableDate::firstOrCreate([
            'property_id' => $propertyId,
            'date' => $request->date,
        ]);

        return response()->json($date, 201);
    }

    // Quitar un día no disponible
    public function destroy(Request $request, $propertyId)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        UnavailableDate::where([
            'property_id' => $propertyId,
            'date' => $request->date,
        ])->delete();

        return response()->json(['message' => 'Fecha eliminada de no disponible']);
    }
}
