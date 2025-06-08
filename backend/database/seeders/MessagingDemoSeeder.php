<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Property;
use App\Models\Reservation;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Str;

class MessagingDemoSeeder extends Seeder
{
    public function run()
    {
        // Crear usuarios únicos (propietario y huésped)
        $owner = User::firstOrCreate(
            ['email' => 'owner@example.com'],
            [
                'username' => 'propietario_demo',
                'name' => 'Propietario Demo',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
                'remember_token' => Str::random(10),
            ]
        );

        $guest = User::firstOrCreate(
            ['email' => 'guest@example.com'],
            [
                'username' => 'huesped_demo',
                'name' => 'Huésped Demo',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
                'remember_token' => Str::random(10),
            ]
        );

        // Crear propiedad
        $property = Property::create([
            'user_id' => $owner->id,
            'name' => 'Apartamento Centro',
            'location' => 'Madrid',
            'bedrooms' => 2,
            'capacity' => 4,
            'price' => 120,
            'image' => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=600&q=80',
            'description' => 'Bonito apartamento en el centro',
            'amenities' => json_encode(['wifi', 'aire acondicionado']),
            'status' => 'active',
        ]);

        // Crear reserva
        $reservation = Reservation::create([
        'user_id' => $guest->id,
        'property_id' => $property->id,
        'reservation_date' => now()->toDateString(),
        'reservation_time' => '15:00:00',
        'details' => json_encode([
            'status' => 'pending', // Mejor así que 'active'
            'check_in' => now()->addDays(3)->toDateString(),
            'check_out' => now()->addDays(5)->toDateString(),
            'total_price' => 120,
            'guests' => 2,
            'message' => 'Llegaré temprano'
        ]),
        'status' => 'pending',
]);

        // Crear conversación
        $conversation = Conversation::create([
            'reservation_id' => $reservation->id,
            'property_id' => $property->id,
            'guest_id' => $guest->id,
            'owner_id' => $owner->id,
        ]);

        // Mensajes
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $guest->id,
            'text' => 'Hola, ¿puedo hacer el check-in antes?',
            'status' => 'sent'
        ]);
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $owner->id,
            'text' => '¡Por supuesto! Avísame cuando llegues.',
            'status' => 'sent'
        ]);

        // Crea 10 usuarios de prueba (emails aleatorios)
        User::factory()->count(10)->create();
    }
}
