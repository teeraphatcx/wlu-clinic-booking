<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminSeeder::class,
            ServiceSeeder::class,
            TherapistSeeder::class,
            ClinicScheduleSeeder::class,
            TimeSlotSeeder::class,
            SettingsSeeder::class,
            ContentSeeder::class,
            CustomerAndBookingSeeder::class,
        ]);
    }
}
