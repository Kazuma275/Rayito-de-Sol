<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Review",
 *     type="object",
 *     title="Review",
 *     description="Reseña realizada por un usuario para una propiedad",
 *     required={"user_id", "author_name", "review_text", "review_date", "language"},
 *     @OA\Property(property="id", type="integer", readOnly=true, example=1),
 *     @OA\Property(property="user_id", type="integer", example=7),
 *     @OA\Property(property="author_name", type="string", example="Carlos Gómez"),
 *     @OA\Property(property="review_text", type="string", example="Excelente alojamiento, muy recomendable."),
 *     @OA\Property(property="author_image", type="string", format="url", nullable=true, example="https://example.com/avatar.jpg"),
 *     @OA\Property(property="review_date", type="string", format="date", example="2025-06-10"),
 *     @OA\Property(property="language", type="string", example="es"),
 *     @OA\Property(property="property_id", type="integer", example=3)
 * )
 */
class Review extends Model
{
    use HasFactory;

    protected $table = 'reviews';

    protected $fillable = [
        'user_id',
        'author_name',
        'review_text',
        'author_image',
        'review_date',
        'language',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function translations()
    {
        return $this->hasMany(ReviewTranslation::class, 'review_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }
}
