<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

/**
 * @OA\Schema(
 *     schema="Reservation",
 *     type="object",
 *     title="Reservation",
 *     description="Reserva de una propiedad por un huésped",
 *     required={"user_id", "property_id", "reservation_date", "reservation_time"},
 *     @OA\Property(property="id", type="integer", readOnly=true, example=1),
 *     @OA\Property(property="user_id", type="integer", example=4),
 *     @OA\Property(property="property_id", type="integer", example=10),
 *     @OA\Property(property="reservation_date", type="string", format="date", example="2025-07-01"),
 *     @OA\Property(property="reservation_time", type="string", example="15:00"),
 *     @OA\Property(
 *         property="details",
 *         type="object",
 *         description="Detalles adicionales en formato JSON",
 *         @OA\Property(property="check_in", type="string", format="date", example="2025-07-05"),
 *         @OA\Property(property="check_out", type="string", format="date", example="2025-07-10"),
 *         @OA\Property(property="guests", type="integer", example=2),
 *         @OA\Property(property="total_price", type="number", format="float", example=500.00),
 *         @OA\Property(property="status", type="string", example="confirmed"),
 *         @OA\Property(property="booking_reference", type="string", example="ABC123XYZ"),
 *         @OA\Property(property="payment_intent_id", type="string", example="pi_1Hxyz..."),
 *         @OA\Property(property="guest_name", type="string", example="Juan Pérez"),
 *         @OA\Property(property="guest_email", type="string", example="juan@example.com"),
 *         @OA\Property(property="guest_phone", type="string", example="+34600000000"),
 *         @OA\Property(property="created_via", type="string", example="stripe_payment"),
 *         @OA\Property(property="total_nights", type="integer", example=5)
 *     )
 * )
 */
class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'property_id',
        'reservation_date',
        'reservation_time',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
        'reservation_date' => 'date',
    ];

    public function guest()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function conversation()
    {
        return $this->hasOne(Conversation::class);
    }

    public function getStatusAttribute()
    {
        $details = $this->details ?? [];
        return $details['status'] ?? 'pending';
    }

    public function getCheckinDateAttribute()
    {
        $details = $this->details ?? [];
        return isset($details['check_in']) ? Carbon::parse($details['check_in']) : null;
    }

    public function getCheckoutDateAttribute()
    {
        $details = $this->details ?? [];
        return isset($details['check_out']) ? Carbon::parse($details['check_out']) : null;
    }

    public function getGuestsAttribute()
    {
        $details = $this->details ?? [];
        return $details['guests'] ?? 1;
    }

    public function getTotalPriceAttribute()
    {
        $details = $this->details ?? [];
        return $details['total_price'] ?? 0;
    }

    public function getBookingReferenceAttribute()
    {
        $details = $this->details ?? [];
        return $details['booking_reference'] ?? null;
    }

    public function getPaymentIntentIdAttribute()
    {
        $details = $this->details ?? [];
        return $details['payment_intent_id'] ?? null;
    }

    public function getGuestInfoAttribute()
    {
        $details = $this->details ?? [];
        return [
            'name' => $details['guest_name'] ?? '',
            'email' => $details['guest_email'] ?? '',
            'phone' => $details['guest_phone'] ?? '',
        ];
    }

    public function isConfirmed()
    {
        return $this->status === 'confirmed';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isStripeBooking()
    {
        $details = $this->details ?? [];
        return isset($details['created_via']) && $details['created_via'] === 'stripe_payment';
    }

    public function getDurationInNightsAttribute()
    {
        if ($this->checkin_date && $this->checkout_date) {
            return $this->checkin_date->diffInDays($this->checkout_date);
        }

        $details = $this->details ?? [];
        return $details['total_nights'] ?? 0;
    }

    public function scopeConfirmed($query)
    {
        return $query->whereJsonContains('details->status', 'confirmed');
    }

    public function scopePending($query)
    {
        return $query->whereJsonContains('details->status', 'pending');
    }

    public function scopeCancelled($query)
    {
        return $query->whereJsonContains('details->status', 'cancelled');
    }

    public function scopeUpcoming($query)
    {
        return $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(details, '$.check_in')) > ?", [Carbon::now()->format('Y-m-d')]);
    }

    public function scopePast($query)
    {
        return $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(details, '$.check_out')) < ?", [Carbon::now()->format('Y-m-d')]);
    }

    public function scopeStripeBookings($query)
    {
        return $query->whereJsonContains('details->created_via', 'stripe_payment');
    }
}
