<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PropertyDayPrice;

class PropertyDayPriceController extends Controller
{
    // Obtener precios del mes
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

    // Guardar o actualizar precio de un día
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

    // Eliminar precio personalizado de un día
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
