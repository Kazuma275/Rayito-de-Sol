<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Property;
use App\Models\Reservation;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Arr;

class ConversationSeeder extends Seeder
{
    public function run()
    {
        $faker = fake('es_ES');
        $owners = User::where('role', 'owner')->get();
        $guests = User::where('role', 'guest')->get();
        $properties = Property::all();
        $reservations = Reservation::all();

        $guestQuestions = [
            "¡Hola! ¿Se puede hacer check-in antes?",
            "¿Incluye sábanas y toallas?",
            "¿Hay supermercado cerca?",
            "¿Puedo llevar a mi perro?",
            "¿Cuál es la mejor manera de llegar desde el aeropuerto?",
            "¿El parking es gratuito?",
            "¿La piscina está abierta en estas fechas?",
            "¿Hay restaurantes recomendados cerca?",
            "¿Hay opción de cuna para bebé?",
            $faker->sentence(),
            "¿Hay wifi rápido? Trabajo a distancia.",
            "¿Se pueden dejar las maletas tras el check-out?",
            "¿El barrio es tranquilo por la noche?",
        ];
        $ownerResponses = [
            "Por supuesto, avísame cuando llegues.",
            "Sí, incluimos todo lo necesario.",
            "Hay supermercados a 5 minutos andando.",
            "Sí, aceptamos mascotas sin problema.",
            "Te recomiendo venir en taxi o bus.",
            "El parking es gratuito para huéspedes.",
            "La piscina está abierta todo el año.",
            "Hay varios restaurantes a menos de 10 minutos.",
            "Tenemos cuna disponible bajo petición.",
            $faker->sentence(),
            "Sí, el wifi es de fibra óptica.",
            "Podéis dejar las maletas sin problema.",
            "La zona es muy tranquila, ideal para descansar.",
        ];

        // Conversaciones para el 80% de las reservas
        foreach ($reservations as $reservation) {
            if (rand(0, 4) < 4) {
                $property = $reservation->property;
                $guest = $reservation->user;
                $owner = $property->owner;

                // Chequeo para evitar duplicados por unique key
                $exists = Conversation::where('reservation_id', $reservation->id)
                    ->where('property_id', $property->id)
                    ->where('guest_id', $guest->id)
                    ->where('owner_id', $owner->id)
                    ->exists();

                if ($exists || !$property || !$guest || !$owner) {
                    continue;
                }

                $conversation = Conversation::create([
                    'reservation_id' => $reservation->id,
                    'property_id' => $property->id,
                    'guest_id' => $guest->id,
                    'owner_id' => $owner->id,
                    'user_one_id' => $guest->id,
                    'user_two_id' => $owner->id,
                ]);
                $msgs = rand(2, 5);
                for ($m = 0; $m < $msgs; $m++) {
                    Message::create([
                        'conversation_id' => $conversation->id,
                        'sender_id' => $guest->id,
                        'text' => Arr::random($guestQuestions),
                        'status' => 'sent',
                    ]);
                    Message::create([
                        'conversation_id' => $conversation->id,
                        'sender_id' => $owner->id,
                        'text' => Arr::random($ownerResponses),
                        'status' => 'sent',
                    ]);
                }
            }
        }

        // Garantiza que TODOS los guests tengan al menos una conversación
        foreach ($guests as $guest) {
            $hasConversation = Conversation::where('guest_id', $guest->id)->exists();
            if (!$hasConversation) {
                $owner = $owners->random();
                $filtered = $properties->where('user_id', $owner->id);
                $property = $filtered->count() ? $filtered->random() : $properties->random();

                // Chequeo para evitar duplicados por unique key
                $exists = Conversation::whereNull('reservation_id')
                    ->where('property_id', $property->id)
                    ->where('guest_id', $guest->id)
                    ->where('owner_id', $owner->id)
                    ->exists();

                if ($exists) {
                    continue;
                }

                $conversation = Conversation::create([
                    'reservation_id' => null,
                    'property_id' => $property->id,
                    'guest_id' => $guest->id,
                    'owner_id' => $owner->id,
                    'user_one_id' => $guest->id,
                    'user_two_id' => $owner->id,
                ]);
                Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => $guest->id,
                    'text' => '¡Hola! Quería consultar disponibilidad.',
                    'status' => 'sent',
                ]);
                Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => $owner->id,
                    'text' => 'Hola, sí, está disponible. ¿Fechas concretas?',
                    'status' => 'sent',
                ]);
            }
        }

        // Garantiza que TODOS los owners tengan al menos una conversación
        foreach ($owners as $owner) {
            $hasConversation = Conversation::where('owner_id', $owner->id)->exists();
            if (!$hasConversation) {
                $guest = $guests->random();
                $filtered = $properties->where('user_id', $owner->id);
                $property = $filtered->count() ? $filtered->random() : $properties->random();

                // Chequeo para evitar duplicados por unique key
                $exists = Conversation::whereNull('reservation_id')
                    ->where('property_id', $property->id)
                    ->where('guest_id', $guest->id)
                    ->where('owner_id', $owner->id)
                    ->exists();

                if ($exists) {
                    continue;
                }

                $conversation = Conversation::create([
                    'reservation_id' => null,
                    'property_id' => $property->id,
                    'guest_id' => $guest->id,
                    'owner_id' => $owner->id,
                    'user_one_id' => $guest->id,
                    'user_two_id' => $owner->id,
                ]);
                Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => $guest->id,
                    'text' => 'Hola, ¿tienes la casa libre en agosto?',
                    'status' => 'sent',
                ]);
                Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => $owner->id,
                    'text' => 'Sí, estaría disponible, ¿para cuántas personas?',
                    'status' => 'sent',
                ]);
            }
        }
    }
}
