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
        // 'user_id',
    ];

    protected $casts = [
        'amenities' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
