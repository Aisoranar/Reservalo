<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\Reservation;
use Illuminate\Support\Facades\Hash;

class ReservaloSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener usuarios existentes (ya creados por UserSeeder)
        $admin = User::where('email', 'admin@reservalo.com')->first();
        $users = User::where('role', 'user')->get();

        // Crear propiedades de prueba
        $properties = [
            [
                'name' => 'Casa de Playa Paradise',
                'description' => 'Hermosa casa frente al mar con vista panorámica, piscina privada y acceso directo a la playa. Ideal para familias o grupos de amigos que buscan relajarse y disfrutar del océano.',
                'location' => 'Playa del Carmen, Quintana Roo',
                'price' => 150.00,
                'capacity' => 6,
                'type' => 'house',
                'services' => ['WiFi', 'Aire acondicionado', 'Piscina', 'Estacionamiento', 'Cocina', 'TV']
            ],
            [
                'name' => 'Hotel Boutique Central',
                'description' => 'Elegante hotel boutique en el corazón de la ciudad, con habitaciones modernas, restaurante gourmet y spa. Perfecto para viajes de negocios o escapadas románticas.',
                'location' => 'Ciudad de México, CDMX',
                'price' => 200.00,
                'capacity' => 2,
                'type' => 'hotel',
                'services' => ['WiFi', 'Aire acondicionado', 'Restaurante', 'Spa', 'Gimnasio', 'Room service']
            ],
            [
                'name' => 'Finca Rural Los Pinos',
                'description' => 'Acogedora finca en medio de la naturaleza, con cabañas rústicas, senderos para caminar y actividades al aire libre. Ideal para desconectarse y disfrutar de la tranquilidad rural.',
                'location' => 'Valle de Bravo, Estado de México',
                'price' => 120.00,
                'capacity' => 4,
                'type' => 'farm',
                'services' => ['WiFi', 'Calefacción', 'Jardín', 'Senderos', 'Actividades al aire libre', 'Desayuno incluido']
            ],
            [
                'name' => 'Apartamento Vista Ciudad',
                'description' => 'Moderno apartamento con vista espectacular a la ciudad, completamente equipado y ubicado en zona céntrica. Perfecto para viajeros que buscan comodidad y ubicación privilegiada.',
                'location' => 'Guadalajara, Jalisco',
                'price' => 180.00,
                'capacity' => 3,
                'type' => 'apartment',
                'services' => ['WiFi', 'Aire acondicionado', 'Cocina', 'TV', 'Estacionamiento', 'Terraza']
            ],
            [
                'name' => 'Casa Colonial Histórica',
                'description' => 'Hermosa casa colonial restaurada con arquitectura tradicional, jardines centenarios y decoración auténtica. Una experiencia única para sumergirse en la historia y cultura local.',
                'location' => 'San Miguel de Allende, Guanajuato',
                'price' => 250.00,
                'capacity' => 8,
                'type' => 'house',
                'services' => ['WiFi', 'Aire acondicionado', 'Jardín', 'Terraza', 'Cocina', 'Biblioteca']
            ]
        ];

        foreach ($properties as $propertyData) {
            $property = Property::create($propertyData);

            // Crear imágenes de ejemplo para cada propiedad
            for ($i = 1; $i <= 3; $i++) {
                PropertyImage::create([
                    'property_id' => $property->id,
                    'url' => "properties/sample_{$property->id}_{$i}.jpg",
                    'alt_text' => $property->name,
                    'is_primary' => $i === 1,
                    'order' => $i - 1
                ]);
            }
        }

        // Crear reservas de prueba
        $properties = Property::all();

        foreach ($users as $user) {
            // Crear 2-3 reservas por usuario
            for ($i = 0; $i < rand(2, 3); $i++) {
                $property = $properties->random();
                $startDate = now()->addDays(rand(10, 60));
                $endDate = $startDate->copy()->addDays(rand(1, 7));
                
                $status = rand(0, 2) === 0 ? 'pending' : (rand(0, 1) === 0 ? 'approved' : 'rejected');
                
                $reservation = Reservation::create([
                    'user_id' => $user->id,
                    'property_id' => $property->id,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'status' => $status,
                    'total_price' => $property->price * $startDate->diffInDays($endDate),
                    'special_requests' => rand(0, 1) === 0 ? 'Llegada temprana si es posible' : null,
                    'rejection_reason' => $status === 'rejected' ? 'Fechas no disponibles' : null
                ]);
            }
        }

        $this->command->info('¡Datos de prueba de Reservalo creados exitosamente!');
        $this->command->info('Usuario administrador: admin@reservalo.com / password');
        $this->command->info('Usuarios de prueba: juan@example.com, maria@example.com, carlos@example.com / password');
    }
}
