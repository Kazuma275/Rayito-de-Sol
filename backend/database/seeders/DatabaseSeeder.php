<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {

        $this->call(MessagingDemoSeeder::class);

        $this->call([
        RoleSeeder::class,
        AdminUserSeeder::class,
        UserSeeder::class,
        PropertySeeder::class,
        ReservationSeeder::class,
        ConversationSeeder::class,
        ]);

    }
}
