<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = [
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
    ];
}
