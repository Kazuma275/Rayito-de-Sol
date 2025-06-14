<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    // Propietario de la propiedad
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Reservas de la propiedad
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    // Conversaciones asociadas a esta propiedad
    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    // Días no disponibles (añade esto)
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
