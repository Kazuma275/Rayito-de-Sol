<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
