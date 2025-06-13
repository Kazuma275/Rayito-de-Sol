<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="UnavailableDate",
 *     type="object",
 *     title="UnavailableDate",
 *     description="Fecha no disponible para una propiedad",
 *     required={"property_id", "date"},
 *     @OA\Property(property="id", type="integer", readOnly=true, example=1),
 *     @OA\Property(property="property_id", type="integer", example=3),
 *     @OA\Property(property="date", type="string", format="date", example="2025-07-15")
 * )
 */
class UnavailableDate extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'property_id',
        'date',
    ];
}
