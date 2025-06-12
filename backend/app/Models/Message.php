<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'text',
        'attachment',
        'status',
        'read_at',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'read_at',
    ];

    // ... relaciones

    // Mutator para cifrar
    public function setTextAttribute($value)
    {
        $this->attributes['text'] = encrypt($value);
    }

    // Accessor para descifrar
    public function getTextAttribute($value)
    {
        if (!$value) return null;
        try {
            return decrypt($value);
        } catch (\Exception $e) {
            return $value; // Por si hay mensajes viejos sin cifrar
        }
    }
}
