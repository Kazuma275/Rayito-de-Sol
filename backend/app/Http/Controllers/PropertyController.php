<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;

class PropertyController extends Controller
{
    public function store(Request $request)
    {
        $property = Property::create($request->all());
        return response()->json($property, 201);
    }
    public function index()
    {
    return response()->json(Property::all());
    }
}
