<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $table = 'reservations';

    protected $fillable = [
        'user_id',
        'property_id',
        'reservation_date',
        'reservation_time',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function property()
    {
        return $this->belongsTo(\App\Models\Property::class, 'property_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
