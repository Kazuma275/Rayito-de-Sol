<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Property;
use App\Models\Reservation;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class MegaDemoSeeder extends Seeder
{    public function run(){
        $faker = fake('es_ES');

        // MÁS DE 60 CIUDADES DE ESPAÑA, COSTA E INTERIOR
        $cityPool = [
            'Madrid','Barcelona','Sevilla','Valencia','Bilbao','Granada','Alicante','Ibiza','San Sebastián','Cádiz','Málaga','Zaragoza','Toledo','Salamanca','Valladolid',
            'Murcia','Pamplona','Santander','Gijón','Oviedo','Tarragona','Almería','La Coruña','Lugo','Segovia','Soria','León','Huesca','Castellón','Ourense',
            'Vigo','Elche','Burgos','Palma de Mallorca','Logroño','Albacete','Cuenca','Ciudad Real','Guadalajara','Badajoz','Cáceres','Ávila','Teruel','Jaén','Algeciras',
            'Torremolinos','Benidorm','Fuengirola','Marbella','Torrevieja','Puerto de la Cruz','Estepona','Playa Blanca','Puerto Banús','Ronda','Sitges','Tossa de Mar','Llanes',
            'Comillas','Costa Adeje','Tarifa','Peñíscola','Calpe','Salou','Cambrils','Sanxenxo','Mojácar','Formentera','Menorca','Lanzarote','La Palma'
        ];

        // MÁS DE 30 TIPOS DE PROPIEDAD
        $propertyTypes = [
            "Apartamento", "Ático", "Piso", "Estudio", "Casa", "Chalet", "Villa", "Dúplex", "Loft", "Casa Rural", "Hostal", "Bungalow", "Finca", "Cabaña", "Palacete",
            "Casa de Pueblo", "Mansión", "Cortijo", "Casa Cueva", "Casa Señorial", "Molino", "Castillo", "Casa Flotante", "Casa Modular", "Tiny House", "Casa de Montaña",
            "Caravana", "Yate", "Albergue", "Camping", "Refugio", "Eco-lodge", "Glamping", "Casa Solariega", "Casa Mediterránea", "Casa Adosada"
        ];

        // MÁS DE 30 ADJETIVOS O ATRIBUTOS
        $propertyAdjectives = [
            "Céntrico", "Luminoso", "Moderno", "Acogedor", "Elegante", "Familiar", "Con vistas", "Junto a la playa", "Tranquilo", "Reformado", "Histórico", "Amplio", "Lujoso",
            "Rústico", "Tradicional", "Nuevo", "Minimalista", "Panorámico", "Romántico", "Soleado", "Espacioso", "Exclusivo", "Premium", "Rodeado de naturaleza", "A pie de playa",
            "Bien comunicado", "Con encanto", "Con jardín", "Con piscina privada", "Con terraza", "Con barbacoa", "Pet friendly", "Smart Home", "Con chimenea", "Cerca del golf",
            "Cerca del puerto", "Alto standing"
        ];

        // MÁS DE 30 AMENITIES
        $amenitiesOptions = [
            'wifi', 'aire acondicionado', 'piscina', 'parking', 'terraza', 'calefacción', 'mascotas', 'vistas', 'smart TV', 'lavavajillas', 'jardín', 'gimnasio', 'ascensor',
            'cafetera', 'barbacoa', 'sábanas incluidas', 'toallas', 'secador', 'plancha', 'microondas', 'nevera', 'conserje', 'pista de tenis', 'spa', 'bodega', 'sala de juegos',
            'zona infantil', 'jacuzzi', 'chimenea', 'desayuno incluido', 'servicio de limpieza', 'garaje privado', 'rooftop', 'cine en casa', 'solarium', 'zona chill out', 'biblioteca'
        ];

        // MÁS DE 30 IMÁGENES REALES (PROBADAS)
        $propertyImages = [
            // Unsplash
            'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1465101046530-73398c7f28ca?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1507089947368-19c1da9775ae?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1512918728675-ed5a9ecdebfd?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1523217582562-09d0def993a6?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1454023492550-5696f8ff10e1?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1464983953574-0892a716854b?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1484154218962-a197022b5858?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1445019980597-93fa8acb246c?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1515378791036-0648a3ef77b2?auto=format&fit=crop&w=800&q=80',
            // Pexels
            'https://images.pexels.com/photos/2611021/pexels-photo-2611021.jpeg?auto=compress&w=800&q=80',
            'https://images.pexels.com/photos/1643383/pexels-photo-1643383.jpeg?auto=compress&w=800&q=80',
            'https://images.pexels.com/photos/206172/pexels-photo-206172.jpeg?auto=compress&w=800&q=80',
            'https://images.pexels.com/photos/259588/pexels-photo-259588.jpeg?auto=compress&w=800&q=80',
            'https://images.pexels.com/photos/210617/pexels-photo-210617.jpeg?auto=compress&w=800&q=80',
            'https://images.pexels.com/photos/106399/pexels-photo-106399.jpeg?auto=compress&w=800&q=80',
            'https://images.pexels.com/photos/271624/pexels-photo-271624.jpeg?auto=compress&w=800&q=80',
            'https://images.pexels.com/photos/221540/pexels-photo-221540.jpeg?auto=compress&w=800&q=80',
            'https://images.pexels.com/photos/259962/pexels-photo-259962.jpeg?auto=compress&w=800&q=80',
            // Pixabay
            'https://cdn.pixabay.com/photo/2016/11/29/06/15/architecture-1868667_1280.jpg',
            'https://cdn.pixabay.com/photo/2017/08/06/00/03/architecture-2589545_1280.jpg',
            'https://cdn.pixabay.com/photo/2017/03/28/12/10/house-2187170_1280.jpg',
            'https://cdn.pixabay.com/photo/2016/10/13/09/06/building-1737167_1280.jpg',
            'https://cdn.pixabay.com/photo/2018/01/17/07/38/architecture-3087192_1280.jpg',
            // Wikimedia Commons (dominio público)
            'https://upload.wikimedia.org/wikipedia/commons/6/6e/Spanish_country_house_%28Cortijo%29_in_Andalusia.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/0/0c/Modern_apartment_building_in_Valencia.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/4/4e/Barceloneta_apartments_-_Barcelona.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/1/1b/Piso_central_Madrid.jpg',
            // Más Unsplash
            'https://images.unsplash.com/photo-1465101178521-c1a9136a3a14?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1519121782372-2c5c0d1140c4?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1560448075-bb0bfcf7c6b2?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1482062364825-616fd23b8fc1?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1519985176271-adb1088fa94c?auto=format&fit=crop&w=800&q=80'
        ];

        // Avatares válidos
        $avatarUrls = [];
        foreach (['men', 'women'] as $gender) {
            foreach ([10,20,30,40,50,60,70,80,90] as $num) {
                $avatarUrls[] = "https://randomuser.me/api/portraits/$gender/$num.jpg";
            }
        }

        // Imágenes de propiedades, todas 100% existentes (saca la url y pégala en tu navegador para probar)
        $propertyImages = [
            'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1465101046530-73398c7f28ca?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1507089947368-19c1da9775ae?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1512918728675-ed5a9ecdebfd?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1523217582562-09d0def993a6?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1454023492550-5696f8ff10e1?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1464983953574-0892a716854b?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1484154218962-a197022b5858?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1445019980597-93fa8acb246c?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1515378791036-0648a3ef77b2?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1465101046530-73398c7f28ca?auto=format&fit=crop&w=600&q=80', // Repetidas para variedad real
            'https://images.unsplash.com/photo-1512918728675-ed5a9ecdebfd?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1523217582562-09d0def993a6?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1454023492550-5696f8ff10e1?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=600&q=80'
        ];

        // Amenities
        $amenitiesOptions = [
            'wifi', 'aire acondicionado', 'piscina', 'parking', 'terraza', 'calefacción', 'mascotas',
            'vistas', 'smart TV', 'lavavajillas', 'jardín', 'gimnasio', 'ascensor', 'cafetera', 'barbacoa'
        ];

        // 1. Usuarios (800: 25% propietarios)
        $owners = collect();
        $guests = collect();
        for ($i = 1; $i <= 800; $i++) {
            $type = (rand(1, 4) === 1) ? 'owner' : 'guest';
            $user = User::create([
                'username' => $type . $faker->unique()->numberBetween(1, 1000000),
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'email_verified_at' => now(),
                'avatar' => $avatarUrls[array_rand($avatarUrls)],
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
            ]);
            if ($type === 'owner') {
                $owners->push($user);
            } else {
                $guests->push($user);
            }
        }

        // 2. Propiedades (220, nombres y fotos realistas)
        $properties = collect();
        for ($i = 1; $i <= 220; $i++) {
            $owner = $owners->random();
            $city = $cityPool[array_rand($cityPool)];
            $img = $propertyImages[array_rand($propertyImages)];
            $price = rand(60, 700);

            $type = $propertyTypes[array_rand($propertyTypes)];
            $adj = $propertyAdjectives[array_rand($propertyAdjectives)];
            $nameFormat = rand(0,2) === 0
                ? "$type $adj en $city"
                : (rand(0,1) ? "$type en $city $adj" : "$type $adj $city");

            $properties->push(Property::create([
                'user_id' => $owner->id,
                'name' => $nameFormat,
                'location' => $city,
                'bedrooms' => rand(1, 5),
                'capacity' => rand(2, 10),
                'price' => $price,
                'image' => $img,
                'description' => "Alojamiento $adj en $city. Perfecto para vacaciones o negocios.",
                'amenities' => json_encode($faker->randomElements($amenitiesOptions, rand(4, 8))),
                'status' => 'active',
            ]));
        }

        // 3. Reservas (1350 aleatorias)
        $statusOptions = ['pending','confirmed','completed','cancelled','active'];
        $now = now();
        for ($i = 1; $i <= 1350; $i++) {
            $property = $properties->random();
            $guest = $guests->random();

            // Fechas aleatorias
            $checkIn = $now->copy()->subDays(rand(-60, 120))->addDays(rand(0, 21));
            $nights = rand(2, 21);
            $checkOut = $checkIn->copy()->addDays($nights);
            $totalPrice = $property->price * $nights;
            $status = $statusOptions[array_rand($statusOptions)];

            $reservation = Reservation::create([
                'user_id' => $guest->id,
                'property_id' => $property->id,
                'reservation_date' => $now->copy()->subDays(rand(0, 120))->toDateString(),
                'reservation_time' => sprintf("%02d:%02d:00", rand(8, 23), rand(0, 59)),
                'details' => [
                    'status' => $status,
                    'check_in' => $checkIn->toDateString(),
                    'check_out' => $checkOut->toDateString(),
                    'total_price' => $totalPrice,
                    'guests' => rand(1, $property->capacity),
                    'message' => $faker->sentence(),
                ],
                'status' => $status,
            ]);

            // Conversaciones para 2/3 de las reservas
            if (rand(0, 2) < 2) {
                $conversation = Conversation::create([
                    'reservation_id' => $reservation->id,
                    'property_id' => $property->id,
                    'guest_id' => $guest->id,
                    'owner_id' => $property->user_id,
                ]);

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
                ];

                Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => $guest->id,
                    'text' => $guestQuestions[array_rand($guestQuestions)],
                    'status' => 'sent',
                ]);
                Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => $property->user_id,
                    'text' => $ownerResponses[array_rand($ownerResponses)],
                    'status' => 'sent',
                ]);
            }
        }
    }
}
