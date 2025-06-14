<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $faker = fake('es_ES');
        $avatarUrls = [];
        foreach (['men', 'women'] as $gender) {
            foreach (range(1, 99) as $num) {
                $avatarUrls[] = "https://randomuser.me/api/portraits/$gender/$num.jpg";
            }
        }

        $totalUsers = 200;
        $adminCount = 3;
        $ownerCount = intval($totalUsers * 0.25); // 25% owners (50)
        $guestCount = $totalUsers - $ownerCount - $adminCount; // El resto guests (147)

        // Admins
        for ($i = 1; $i <= $adminCount; $i++) {
            User::create([
                'username' => 'admin' . $faker->unique()->numberBetween(1, 1000000),
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'email_verified_at' => now(),
                'avatar' => $avatarUrls[array_rand($avatarUrls)],
                'password' => Hash::make('adminpassword'),
                'remember_token' => Str::random(10),
                'role' => 'admin',
                'phone' => self::fakeSpanishMobile($faker),
            ]);
        }

        // Owners (con propiedades)
        for ($i = 1; $i <= $ownerCount; $i++) {
            User::create([
                'username' => 'owner' . $faker->unique()->numberBetween(1, 1000000),
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'email_verified_at' => now(),
                'avatar' => $avatarUrls[array_rand($avatarUrls)],
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
                'role' => 'owner',
                'phone' => self::fakeSpanishMobile($faker),
            ]);
        }

        // Guests (sin propiedades)
        for ($i = 1; $i <= $guestCount; $i++) {
            User::create([
                'username' => 'guest' . $faker->unique()->numberBetween(1, 1000000),
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'email_verified_at' => now(),
                'avatar' => $avatarUrls[array_rand($avatarUrls)],
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
                'role' => 'guest',
                'phone' => self::fakeSpanishMobile($faker),
            ]);
        }
    }

    /**
     * Genera un número de móvil español realista en formato "+34 6XX XXX XXX"
     */
    private static function fakeSpanishMobile($faker)
    {
        // El prefijo móvil español normalmente empieza por 6 o 7
        $prefix = $faker->randomElement(['6', '7']);
        $numbers = $faker->numerify('## ### ###');
        return "+34 {$prefix}{$numbers}";
    }
}
