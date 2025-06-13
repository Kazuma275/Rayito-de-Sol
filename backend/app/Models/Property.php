<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *     schema="Property",
 *     type="object",
 *     title="Property",
 *     description="Modelo que representa una propiedad para alquiler",
 *     required={"user_id", "name", "location", "bedrooms", "capacity", "price", "status"},
 *     @OA\Property(property="id", type="integer", readOnly=true, example=1),
 *     @OA\Property(property="user_id", type="integer", example=5),
 *     @OA\Property(property="name", type="string", example="Casa de Playa"),
 *     @OA\Property(property="location", type="string", example="Barcelona, España"),
 *     @OA\Property(property="bedrooms", type="integer", example=3),
 *     @OA\Property(property="capacity", type="integer", example=6),
 *     @OA\Property(property="price", type="number", format="float", example=120.50),
 *     @OA\Property(property="image", type="string", format="url", example="https://example.com/imagen.jpg"),
 *     @OA\Property(property="description", type="string", example="Hermosa casa frente al mar."),
 *     @OA\Property(property="amenities", type="array", @OA\Items(type="string"), example={"WiFi", "Piscina", "Aire acondicionado"}),
 *     @OA\Property(property="status", type="string", example="disponible"),
 *     @OA\Property(property="rating", type="number", format="float", readOnly=true, example=4.8),
 * )
 */
class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'location',
        'bedrooms',
        'capacity',
        'price',
        'image',
        'description',
        'amenities',
        'status',
    ];

    protected $casts = [
        'amenities' => 'array',
        'images' => 'array',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function unavailableDates()
    {
        return $this->hasMany(UnavailableDate::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'property_id');
    }

    public function getRatingAttribute()
    {
        return round($this->reviews()->avg('rating') ?? 4.5, 1);
    }
}
