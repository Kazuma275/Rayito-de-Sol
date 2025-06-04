<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyDayPrice extends Model
{
    protected $fillable = [
        'property_id',
        'date',
        'price',
        'notes',
    ];
}
