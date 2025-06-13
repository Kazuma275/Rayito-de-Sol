<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Imágenes asociadas a propiedades (galería).
 *
 * @OA\Schema(
 *   schema="Gallery",
 *   type="object",
 *   required={"id", "name", "image_path"},
 *   @OA\Property(property="id", type="integer", example=1, description="ID único de la imagen"),
 *   @OA\Property(property="name", type="string", example="Foto principal", description="Nombre o descripción de la imagen"),
 *   @OA\Property(property="image_path", type="string", example="images/properties/1234.jpg", description="Ruta o URL de la imagen"),
 *   @OA\Property(property="property_id", type="integer", example=10, description="ID de la propiedad a la que pertenece (si aplica)"),
 *   @OA\Property(property="created_at", type="string", format="date-time", example="2025-06-13T10:20:00Z"),
 *   @OA\Property(property="updated_at", type="string", format="date-time", example="2025-06-13T10:20:00Z")
 * )
 */
class Gallery extends Model
{
    use HasFactory;

    protected $table = 'gallery';

    protected $fillable = [
        'name',
        'image_path',
    ];
}
