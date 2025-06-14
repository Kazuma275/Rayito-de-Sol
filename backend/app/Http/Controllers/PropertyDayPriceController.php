<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PropertyDayPrice;

class PropertyDayPriceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/properties/{propertyId}/day-prices",
     *     summary="Obtener los precios diarios personalizados del mes para una propiedad",
     *     tags={"Propiedades"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="propertyId",
     *         in="path",
     *         required=true,
     *         description="ID de la propiedad",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="year",
     *         in="query",
     *         required=false,
     *         description="Año (YYYY). Por defecto el año actual.",
     *         @OA\Schema(type="integer", example=2025)
     *     ),
     *     @OA\Parameter(
     *         name="month",
     *         in="query",
     *         required=false,
     *         description="Mes (1-12). Por defecto el mes actual.",
     *         @OA\Schema(type="integer", example=7)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Precios diarios del mes consultado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="2025-07-01",
     *                 type="object",
     *                 @OA\Property(property="price", type="number", format="float", example=120.00),
     *                 @OA\Property(property="notes", type="string", example="Precio especial por evento")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function index(Request $request, $propertyId)
    {
        $year = $request->query('year', date('Y'));
        $month = $request->query('month', date('n'));
        $start = "{$year}-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-01";
        $end = date('Y-m-t', strtotime($start));
        $prices = PropertyDayPrice::where('property_id', $propertyId)
            ->whereBetween('date', [$start, $end])
            ->get();

        $result = [];
        foreach ($prices as $row) {
            $result[$row->date] = [
                'price' => $row->price,
                'notes' => $row->notes,
            ];
        }
        return response()->json($result);
    }

    /**
     * @OA\Post(
     *     path="/api/properties/{propertyId}/day-prices",
     *     summary="Crear o actualizar el precio personalizado para un día concreto en una propiedad",
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
     *             required={"date", "price"},
     *             @OA\Property(property="date", type="string", format="date", example="2025-07-01"),
     *             @OA\Property(property="price", type="number", format="float", example=150.00),
     *             @OA\Property(property="notes", type="string", example="Precio por temporada alta")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Precio guardado o actualizado correctamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true)
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
     *         response=401,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function store(Request $request, $propertyId)
    {
        $request->validate([
            'date' => 'required|date',
            'price' => 'required|numeric|min:0',
        ]);
        $data = [
            'property_id' => $propertyId,
            'date' => $request->input('date'),
        ];
        PropertyDayPrice::updateOrCreate($data, [
            'price' => $request->input('price'),
            'notes' => $request->input('notes'),
        ]);
        return response()->json(['success' => true]);
    }

    /**
     * @OA\Delete(
     *     path="/api/properties/{propertyId}/day-prices",
     *     summary="Eliminar el precio personalizado de un día concreto en una propiedad",
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
     *             @OA\Property(property="date", type="string", format="date", example="2025-07-01")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Precio eliminado correctamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true)
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
     *         response=401,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function destroy(Request $request, $propertyId)
    {
        $request->validate([
            'date' => 'required|date',
        ]);
        PropertyDayPrice::where([
            'property_id' => $propertyId,
            'date' => $request->input('date'),
        ])->delete();
        return response()->json(['success' => true]);
    }
}
