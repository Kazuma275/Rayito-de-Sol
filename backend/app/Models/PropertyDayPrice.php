<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="PropertyDayPrice",
 *     type="object",
 *     title="PropertyDayPrice",
 *     description="Precio específico por día para una propiedad",
 *     required={"property_id", "date", "price"},
 *     @OA\Property(property="id", type="integer", readOnly=true, example=1),
 *     @OA\Property(property="property_id", type="integer", example=12),
 *     @OA\Property(property="date", type="string", format="date", example="2025-07-15"),
 *     @OA\Property(property="price", type="number", format="float", example=150.00),
 *     @OA\Property(property="notes", type="string", nullable=true, example="Precio especial por temporada alta")
 * )
 */
class PropertyDayPrice extends Model
{
    protected $fillable = [
        'property_id',
        'date',
        'price',
        'notes',
    ];
}
