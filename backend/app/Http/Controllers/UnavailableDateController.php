<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UnavailableDate;

class UnavailableDateController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/properties/{propertyId}/unavailable-dates",
     *     summary="Obtener todos los días no disponibles para una propiedad",
     *     tags={"Propiedades"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="propertyId",
     *         in="path",
     *         required=true,
     *         description="ID de la propiedad",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de fechas no disponibles (array de fechas YYYY-MM-DD)",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(type="string", format="date", example="2025-07-10")
     *         )
     *     )
     * )
     */
    public function index($propertyId)
    {
        $dates = UnavailableDate::where('property_id', $propertyId)->pluck('date');
        return response()->json($dates);
    }

    /**
     * @OA\Post(
     *     path="/api/properties/{propertyId}/unavailable-dates",
     *     summary="Agregar un día no disponible a una propiedad",
     *     tags={"Propiedades"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="propertyId",
     *         in="path",
     *         required=true,
     *         description="ID de la propiedad",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"date"},
     *             @OA\Property(property="date", type="string", format="date", example="2025-07-10")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Día no disponible agregado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=5),
     *             @OA\Property(property="property_id", type="integer", example=1),
     *             @OA\Property(property="date", type="string", format="date", example="2025-07-10"),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2025-06-13T10:39:13Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2025-06-13T10:39:13Z")
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

    /**
     * @OA\Delete(
     *     path="/api/properties/{propertyId}/unavailable-dates",
     *     summary="Eliminar un día no disponible de una propiedad",
     *     tags={"Propiedades"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="propertyId",
     *         in="path",
     *         required=true,
     *         description="ID de la propiedad",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"date"},
     *             @OA\Property(property="date", type="string", format="date", example="2025-07-10")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Fecha eliminada de no disponible",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Fecha eliminada de no disponible")
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
