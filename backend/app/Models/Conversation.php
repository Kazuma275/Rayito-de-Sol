<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Conversation extends Model
{
    use HasFactory;

    protected $table = 'conversations';

    protected $fillable = [
        'user_one_id',
        'user_two_id',
        'property_id',
        'reservation_id',
        'guest_id',
        'owner_id',
        'property_id',
        'reservation_id',
        'starred_by_guest',
        'starred_by_owner',
        'archived_by_guest',
        'archived_by_owner',
    ];

    // Relaciones idénticas a las que tienes
    public function guest() { return $this->belongsTo(User::class, 'guest_id'); }
    public function owner() { return $this->belongsTo(User::class, 'owner_id'); }
    public function messages() { return $this->hasMany(Message::class); }
    public function property() { return $this->belongsTo(Property::class); }
    public function reservation() { return $this->belongsTo(Reservation::class); }
    // Relaciones:
    public function userOne() {
        return $this->belongsTo(User::class, 'user_one_id');
    }
    public function userTwo() {
        return $this->belongsTo(User::class, 'user_two_id');
    }
    public function lastMessage()
{
    return $this->hasOne(Message::class)->latestOfMany();
}
}
