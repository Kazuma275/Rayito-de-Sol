<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="ReviewTranslation",
 *     type="object",
 *     title="ReviewTranslation",
 *     description="Traducción de una reseña",
 *     required={"review_id", "language_code", "review_text"},
 *     @OA\Property(property="id", type="integer", readOnly=true, example=1),
 *     @OA\Property(property="review_id", type="integer", example=5),
 *     @OA\Property(property="language_code", type="string", example="en"),
 *     @OA\Property(property="review_text", type="string", example="Excellent accommodation, highly recommended.")
 * )
 */
class ReviewTranslation extends Model
{
    use HasFactory;

    protected $table = 'review_translations';

    protected $fillable = [
        'review_id',
        'language_code',
        'review_text',
    ];

    public function review()
    {
        return $this->belongsTo(Review::class, 'review_id');
    }
}
