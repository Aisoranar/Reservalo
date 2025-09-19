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
            
            // Seeders de ubicaciones
            ColombianDepartmentsSeeder::class,
            ColombianCitiesSeeder::class,
            
            // Seeders de propiedades
            ReservaloSeeder::class,
            SamplePropertiesSeeder::class,
            PropertyImagesSeeder::class,
            
            // Seeders de precios (en orden correcto)
            GlobalPricingSeeder::class,
            NightlyPricesSeeder::class,
            DiscountsSeeder::class,
            
            // Seeders de reservas (después de precios)
            SampleReservationsSeeder::class,
            
            // Seeders de plantillas de email
            TempAccountEmailTemplateSeeder::class,
            AddReservationDeletedEmailTemplateSeeder::class,
            
            // Seeders de permisos adicionales
            AddDeleteReservationsPermissionSeeder::class,
        ]);
    }
}
