<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    // En Review.php
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    // En Property.php
    public function reviews()
    {
        return $this->hasMany(Review::class, 'property_id');
    }
}
