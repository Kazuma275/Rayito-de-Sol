<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

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
        'reservation_date' => 'date',
    ];

    // Huésped que realizó la reserva
    public function guest()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Propietario de la propiedad reservada
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
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

    // NUEVOS MÉTODOS PARA STRIPE INTEGRATION

    // Obtener el estado de la reserva
    public function getStatusAttribute()
    {
        $details = $this->details ?? [];
        return $details['status'] ?? 'pending';
    }

    // Obtener fecha de check-in
    public function getCheckinDateAttribute()
    {
        $details = $this->details ?? [];
        return isset($details['check_in']) ? Carbon::parse($details['check_in']) : null;
    }

    // Obtener fecha de check-out
    public function getCheckoutDateAttribute()
    {
        $details = $this->details ?? [];
        return isset($details['check_out']) ? Carbon::parse($details['check_out']) : null;
    }

    // Obtener número de huéspedes
    public function getGuestsAttribute()
    {
        $details = $this->details ?? [];
        return $details['guests'] ?? 1;
    }

    // Obtener precio total
    public function getTotalPriceAttribute()
    {
        $details = $this->details ?? [];
        return $details['total_price'] ?? 0;
    }

    // Obtener referencia de reserva
    public function getBookingReferenceAttribute()
    {
        $details = $this->details ?? [];
        return $details['booking_reference'] ?? null;
    }

    // Obtener Payment Intent ID
    public function getPaymentIntentIdAttribute()
    {
        $details = $this->details ?? [];
        return $details['payment_intent_id'] ?? null;
    }

    // Obtener información del huésped
    public function getGuestInfoAttribute()
    {
        $details = $this->details ?? [];
        return [
            'name' => $details['guest_name'] ?? '',
            'email' => $details['guest_email'] ?? '',
            'phone' => $details['guest_phone'] ?? '',
        ];
    }

    // Verificar si la reserva está confirmada
    public function isConfirmed()
    {
        return $this->status === 'confirmed';
    }

    // Verificar si la reserva está cancelada
    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    // Verificar si la reserva está pendiente
    public function isPending()
    {
        return $this->status === 'pending';
    }

    // Verificar si la reserva fue creada vía Stripe
    public function isStripeBooking()
    {
        $details = $this->details ?? [];
        return isset($details['created_via']) && $details['created_via'] === 'stripe_payment';
    }

    // Obtener duración en noches
    public function getDurationInNightsAttribute()
    {
        if ($this->checkin_date && $this->checkout_date) {
            return $this->checkin_date->diffInDays($this->checkout_date);
        }

        $details = $this->details ?? [];
        return $details['total_nights'] ?? 0;
    }

    // Scopes para filtrar reservas
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
