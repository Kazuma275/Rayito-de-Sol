<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Property;
use App\Models\Reservation;
use Carbon\Carbon;

class ReservationSeeder extends Seeder
{
    public function run()
    {
        $faker = fake('es_ES');
        $guests = User::where('role', 'guest')->get();
        $properties = Property::all();
        $statusOptions = ['pending','confirmed','completed','cancelled','active'];
        $now = now();
        $reservationCount = min(1200, $properties->count() * 3); // Más reservas
        $usedDatesByProperty = [];

        for ($i = 1; $i <= $reservationCount; $i++) {
            $property = $properties->random();
            $guest = $guests->random();
            $baseStart = $now->copy()->subDays(rand(-40, 90))->addDays(rand(0, 7));
            $try = 0;
            do {
                $nights = rand(3, 14);
                $checkIn = $baseStart->copy()->addDays(rand(0, 5));
                $checkOut = $checkIn->copy()->addDays($nights);
                $try++;
            } while (
                isset($usedDatesByProperty[$property->id]) &&
                $this->overlaps($checkIn, $checkOut, $usedDatesByProperty[$property->id]) &&
                $try < 10
            );
            $usedDatesByProperty[$property->id][] = [$checkIn->copy(), $checkOut->copy()];
            $totalPrice = $property->price * $nights;
            $status = $statusOptions[array_rand($statusOptions)];

            Reservation::create([
                'user_id' => $guest->id,
                'property_id' => $property->id,
                'reservation_date' => $now->copy()->subDays(rand(0, 90))->toDateString(),
                'reservation_time' => sprintf("%02d:%02d:00", rand(8, 23), rand(0, 59)),
                'details' => [
                    'status' => $status,
                    'check_in' => $checkIn->toDateString(),
                    'check_out' => $checkOut->toDateString(),
                    'total_price' => $totalPrice,
                    'guests' => rand(2, min($property->capacity, 8)),
                    'message' => $faker->sentence(),
                    'pet' => rand(0, 4) === 0 ? $faker->boolean() : null,
                    'airport_transfer' => rand(0, 7) === 0 ? $faker->boolean() : null,
                    'extra_cleaning' => rand(0, 5) === 0 ? $faker->boolean() : null,
                ],
                'status' => $status,
            ]);
        }
    }

    private function overlaps($checkIn, $checkOut, $bookings)
    {
        foreach ($bookings as [$bStart, $bEnd]) {
            if ($checkIn <= $bEnd && $checkOut >= $bStart) {
                return true;
            }
        }
        return false;
    }
}
