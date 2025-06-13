<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="ServiceTranslation",
 *     type="object",
 *     title="ServiceTranslation",
 *     description="Traducción de un servicio",
 *     required={"service_id", "language_code", "service_name", "service_description"},
 *     @OA\Property(property="id", type="integer", readOnly=true, example=1),
 *     @OA\Property(property="service_id", type="integer", example=5),
 *     @OA\Property(property="language_code", type="string", example="en"),
 *     @OA\Property(property="service_name", type="string", example="Cleaning"),
 *     @OA\Property(property="service_description", type="string", example="Professional cleaning service")
 * )
 */
class ServiceTranslation extends Model
{
    use HasFactory;

    protected $table = 'services_translations';

    protected $fillable = [
        'service_id',
        'language_code',
        'service_name',
        'service_description',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
