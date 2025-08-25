<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Reservation;
use App\Models\Property;
use App\Models\User;
use Carbon\Carbon;

class SampleReservationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $properties = Property::all();
        $users = User::all();

        if ($properties->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No hay propiedades o usuarios disponibles. Ejecuta primero los seeders correspondientes.');
            return;
        }

        // Crear reservas con diferentes estados
        $reservations = [
            [
                'status' => 'pending',
                'payment_status' => 'pending',
                'amount_paid' => 0,
                'start_date' => Carbon::now()->addDays(5),
                'end_date' => Carbon::now()->addDays(8),
                'total_price' => 450000,
                'special_requests' => 'Llegada temprana si es posible'
            ],
            [
                'status' => 'approved',
                'payment_status' => 'paid',
                'amount_paid' => 600000,
                'start_date' => Carbon::now()->addDays(10),
                'end_date' => Carbon::now()->addDays(14),
                'total_price' => 600000,
                'special_requests' => null
            ],
            [
                'status' => 'approved',
                'payment_status' => 'partial',
                'amount_paid' => 300000,
                'start_date' => Carbon::now()->addDays(20),
                'end_date' => Carbon::now()->addDays(23),
                'total_price' => 450000,
                'special_requests' => 'Check-in a las 16:00'
            ],
            [
                'status' => 'rejected',
                'payment_status' => 'pending',
                'amount_paid' => 0,
                'start_date' => Carbon::now()->addDays(25),
                'end_date' => Carbon::now()->addDays(28),
                'total_price' => 450000,
                'special_requests' => null,
                'admin_notes' => 'Fechas no disponibles'
            ]
        ];

        foreach ($reservations as $index => $reservationData) {
            $property = $properties->random();
            $user = $users->random();

            Reservation::create([
                'user_id' => $user->id,
                'property_id' => $property->id,
                'status' => $reservationData['status'],
                'payment_status' => $reservationData['payment_status'],
                'amount_paid' => $reservationData['amount_paid'],
                'start_date' => $reservationData['start_date'],
                'end_date' => $reservationData['end_date'],
                'total_price' => $reservationData['total_price'],
                'special_requests' => $reservationData['special_requests'],
                'admin_notes' => $reservationData['admin_notes'] ?? null,
                'approved_at' => $reservationData['status'] === 'approved' ? Carbon::now()->subDays(rand(1, 5)) : null,
                'approved_by' => $reservationData['status'] === 'approved' ? 1 : null,
                'paid_at' => $reservationData['payment_status'] === 'paid' ? Carbon::now()->subDays(rand(1, 3)) : null
            ]);
        }

        $this->command->info('✅ Reservas de ejemplo creadas exitosamente');
        $this->command->info("📅 Se crearon " . count($reservations) . " reservas con diferentes estados");
    }
}
