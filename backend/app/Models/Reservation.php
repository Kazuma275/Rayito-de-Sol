<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'property_id',
        'reservation_date',
        'reservation_time',
        'details',
        // 'status', // Si decides agregarlo
    ];

    protected $casts = [
    'details' => 'array',
    ];

    // Huésped que realizó la reserva
    public function guest()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    // Propiedad reservada
    public function property()
    {
        return $this->belongsTo(Property::class);
    }



    // Conversación asociada a esta reserva (1 a 1)
    public function conversation()
    {
        return $this->hasOne(Conversation::class);
    }
}
