<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Service",
 *     type="object",
 *     title="Service",
 *     description="Servicio ofrecido por un usuario",
 *     required={"user_id", "service_name", "service_description"},
 *     @OA\Property(property="id", type="integer", readOnly=true, example=1),
 *     @OA\Property(property="user_id", type="integer", example=8),
 *     @OA\Property(property="service_name", type="string", example="Limpieza"),
 *     @OA\Property(property="service_description", type="string", example="Servicio de limpieza profesional"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-06-10T14:48:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-06-12T09:30:00Z")
 * )
 */
class Service extends Model
{
    use HasFactory;

    protected $table = 'services';

    protected $fillable = [
        'user_id',
        'service_name',
        'service_description',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function translations()
    {
        return $this->hasMany(ServiceTranslation::class, 'service_id');
    }
}
