<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Seeders del sistema de roles y permisos
            RolePermissionSeeder::class,
            SystemSettingsSeeder::class,
            MembershipPlanSeeder::class,
            UserSeeder::class,
            
            // Seeders existentes
            ColombianDepartmentsSeeder::class,
            ColombianCitiesSeeder::class,
            ReservaloSeeder::class,
            SamplePropertiesSeeder::class,
            PropertyImagesSeeder::class,
            SampleReservationsSeeder::class,
            DiscountsSeeder::class,
            NightlyPricesSeeder::class,
        ]);
    }
}
