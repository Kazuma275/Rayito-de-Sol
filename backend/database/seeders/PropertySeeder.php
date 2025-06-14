<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Property;
use App\Models\Review;

class PropertySeeder extends Seeder
{
    public function run()
    {
        $faker = fake('es_ES');
        $cityPool = [
            'Madrid','Barcelona','Sevilla','Valencia','Bilbao','Granada','Alicante','Ibiza','San Sebastián','Cádiz','Málaga','Zaragoza','Toledo','Salamanca','Valladolid',
            'Murcia','Pamplona','Santander','Gijón','Oviedo','Tarragona','Almería','La Coruña','Lugo','Segovia','Soria','León','Huesca','Castellón','Ourense',
            'Vigo','Elche','Burgos','Palma de Mallorca','Logroño','Albacete','Cuenca','Ciudad Real','Guadalajara','Badajoz','Cáceres','Ávila','Teruel','Jaén','Algeciras',
            'Torremolinos','Benidorm','Fuengirola','Marbella','Torrevieja','Puerto de la Cruz','Estepona','Playa Blanca','Puerto Banús','Ronda','Sitges','Tossa de Mar','Llanes',
            'Comillas','Costa Adeje','Tarifa','Peñíscola','Calpe','Salou','Cambrils','Sanxenxo','Mojácar','Formentera','Menorca','Lanzarote','La Palma'
        ];
        $propertyTypes = [
            "Apartamento", "Ático", "Piso", "Estudio", "Casa", "Chalet", "Villa", "Dúplex", "Loft", "Casa Rural", "Hostal", "Bungalow", "Finca", "Cabaña", "Palacete",
            "Casa de Pueblo", "Mansión", "Cortijo", "Casa Cueva", "Casa Señorial", "Molino", "Castillo", "Casa Flotante", "Casa Modular", "Tiny House", "Casa de Montaña",
            "Caravana", "Yate", "Albergue", "Camping", "Refugio", "Eco-lodge", "Glamping", "Casa Solariega", "Casa Mediterránea", "Casa Adosada"
        ];
        $propertyAdjectives = [
            "Céntrico", "Luminoso", "Moderno", "Acogedor", "Elegante", "Familiar", "Con vistas", "Junto a la playa", "Tranquilo", "Reformado", "Histórico", "Amplio", "Lujoso",
            "Rústico", "Tradicional", "Nuevo", "Minimalista", "Panorámico", "Romántico", "Soleado", "Espacioso", "Exclusivo", "Premium", "Rodeado de naturaleza", "A pie de playa",
            "Bien comunicado", "Con encanto", "Con jardín", "Con piscina privada", "Con terraza", "Con barbacoa", "Pet friendly", "Smart Home", "Con chimenea", "Cerca del golf",
            "Cerca del puerto", "Alto standing"
        ];
        $amenitiesOptions = [
            'wifi', 'aire acondicionado', 'piscina', 'parking', 'terraza', 'calefacción', 'mascotas', 'vistas', 'smart TV', 'lavavajillas', 'jardín', 'gimnasio', 'ascensor',
            'cafetera', 'barbacoa', 'sábanas incluidas', 'toallas', 'secador', 'plancha', 'microondas', 'nevera', 'conserje', 'pista de tenis', 'spa', 'bodega', 'sala de juegos',
            'zona infantil', 'jacuzzi', 'chimenea', 'desayuno incluido', 'servicio de limpieza', 'garaje privado', 'rooftop', 'cine en casa', 'solarium', 'zona chill out', 'biblioteca'
        ];
        $propertyImages = [
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
            'https://images.unsplash.com/photo-1482062364825-616fd23b8fc1?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1519985176271-adb1088fa94c?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1470770841072-f978cf4d019e?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1501183638710-841dd1904471?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1465101046530-73398c7f28ca?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1432888498266-38ffec3eaf0a?auto=format&fit=crop&w=800&q=80',
        ];

        // Obtener propietarios y usuarios para reviews
        $owners = User::where('role', 'owner')->get();
        $users = User::where('role', 'guest')->get();

        foreach ($owners as $owner) {
            // Cada owner tendrá entre 2 y 4 propiedades (mínimo 2)
            $propCount = rand(2, 4);
            for ($j = 0; $j < $propCount; $j++) {
                $city = $cityPool[array_rand($cityPool)];
                $img = $propertyImages[array_rand($propertyImages)];
                $price = rand(70, 800);
                $type = $propertyTypes[array_rand($propertyTypes)];
                $adj = $propertyAdjectives[array_rand($propertyAdjectives)];
                $nameFormat = rand(0,2) === 0
                    ? "$type $adj en $city"
                    : (rand(0,1) ? "$type en $city $adj" : "$type $adj $city");
                $bedrooms = (rand(1, 100) <= 60) ? rand(3, 5) : rand(1, 2);
                $capacity = $bedrooms * rand(2,3) + rand(0, 2);
                $amenitiesCount = rand(7, 13);
                $descriptions = [
                    "Alojamiento $adj en $city. Perfecto para vacaciones o negocios. Disfruta de todas las comodidades y una ubicación excelente.",
                    "Vive una experiencia única en este $type $adj. Ideal para familias, grupos o escapadas románticas en $city.",
                    "Descubre la magia de $city desde este $type $adj. Totalmente equipado y con todos los detalles para una estancia inolvidable.",
                    "¡Bienvenido a tu hogar temporal en $city! Este $type ofrece tranquilidad, confort y un ambiente especial.",
                    "Perfecto para desconectar: $type $adj en $city con $amenitiesCount servicios destacados y ambiente relajado.",
                    "Sumérgete en la vida local de $city en este $type $adj. ¡Te enamorarás de sus vistas y su entorno!",
                    "¿Buscas un alojamiento diferente? Este $type $adj en $city es justo lo que necesitas para unas vacaciones originales.",
                    "$type $adj en $city, con opción de actividades y experiencias personalizadas. Consulta los extras disponibles.",
                ];
                $description = $descriptions[array_rand($descriptions)];

                $property = Property::create([
                    'user_id' => $owner->id,
                    'name' => $nameFormat,
                    'location' => $city,
                    'bedrooms' => $bedrooms,
                    'capacity' => $capacity,
                    'price' => $price,
                    'image' => $img,
                    'description' => $description,
                    'amenities' => json_encode($faker->randomElements($amenitiesOptions, $amenitiesCount)),
                    'status' => 'active',
                ]);

                // Añadir entre 3 y 10 reviews aleatorias por propiedad
                $reviewCount = rand(3, 10);
                if ($users->count() > 0) {
                    for ($i = 0; $i < $reviewCount; $i++) {
                        $reviewUser = $users->random();
                        Review::create([
                            'user_id' => $reviewUser->id,
                            'property_id' => $property->id,
                            'rating' => round(rand(30, 50) / 10, 1), // 3.0 a 5.0
                            'review' => $faker->sentence(rand(8, 18)),
                            'author_name' => $reviewUser->name,
                            'author_image' => $reviewUser->profile_photo_path ?? null,
                            'review_date' => now()->subDays(rand(0, 730)),
                            'language' => 'es',
                        ]);
                    }
                }
            }
        }
    }
}
