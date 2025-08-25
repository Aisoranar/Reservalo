<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservation;
use App\Models\Property;
use App\Models\User;

class CheckReservationsSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:check-system';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar que el sistema de reservas esté funcionando correctamente';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Verificando sistema de reservas...');
        $this->newLine();

        // Verificar reservas
        $this->info('📊 ESTADÍSTICAS DE RESERVAS:');
        $this->info('   Total de reservas: ' . Reservation::count());
        $this->info('   Pendientes: ' . Reservation::where('status', 'pending')->count());
        $this->info('   Aprobadas: ' . Reservation::where('status', 'approved')->count());
        $this->info('   Rechazadas: ' . Reservation::where('status', 'rejected')->count());
        $this->info('   Canceladas: ' . Reservation::where('status', 'cancelled')->count());
        $this->newLine();

        // Verificar estados de pago
        $this->info('💳 ESTADOS DE PAGO:');
        $this->info('   Pendientes: ' . Reservation::where('payment_status', 'pending')->count());
        $this->info('   Pagados: ' . Reservation::where('payment_status', 'paid')->count());
        $this->info('   Parciales: ' . Reservation::where('payment_status', 'partial')->count());
        $this->info('   Reembolsados: ' . Reservation::where('payment_status', 'refunded')->count());
        $this->newLine();

        // Verificar propiedades con reservas
        $this->info('🏠 PROPIEDADES CON RESERVAS:');
        $propertiesWithReservations = Property::whereHas('reservations')->count();
        $this->info('   Propiedades con reservas: ' . $propertiesWithReservations);
        $this->info('   Total de propiedades: ' . Property::count());
        $this->newLine();

        // Verificar usuarios con reservas
        $this->info('👥 USUARIOS CON RESERVAS:');
        $usersWithReservations = User::whereHas('reservations')->count();
        $this->info('   Usuarios con reservas: ' . $usersWithReservations);
        $this->info('   Total de usuarios: ' . User::count());
        $this->newLine();

        // Mostrar algunas reservas de ejemplo
        $this->info('📅 RESERVAS DE EJEMPLO:');
        $sampleReservations = Reservation::with(['user', 'property'])->take(3)->get();
        
        foreach ($sampleReservations as $reservation) {
            $this->line("   • ID: {$reservation->id} | Usuario: {$reservation->user->name} | Propiedad: {$reservation->property->name}");
            $this->line("     Estado: {$reservation->status} | Pago: {$reservation->payment_status} | Fechas: {$reservation->start_date->format('d/m/Y')} - {$reservation->end_date->format('d/m/Y')}");
            $this->line("     Precio: $" . number_format($reservation->total_price, 0) . " | Pagado: $" . number_format($reservation->amount_paid, 0));
            $this->newLine();
        }

        // Verificar disponibilidad
        $this->info('📅 VERIFICANDO DISPONIBILIDAD:');
        $property = Property::first();
        if ($property) {
            $this->info("   Verificando disponibilidad para: {$property->name}");
            
            // Simular llamada a la API de disponibilidad
            try {
                $reservations = Reservation::where('property_id', $property->id)
                    ->whereIn('status', ['approved', 'pending'])
                    ->get();
                
                $this->info("   Reservas encontradas: " . $reservations->count());
                
                foreach ($reservations as $reservation) {
                    $this->line("     • {$reservation->start_date->format('d/m/Y')} - {$reservation->end_date->format('d/m/Y')} ({$reservation->status})");
                }
            } catch (\Exception $e) {
                $this->error("   Error al verificar disponibilidad: " . $e->getMessage());
            }
        }
        
        $this->newLine();
        $this->info('✅ Verificación del sistema de reservas completada');
        
        return Command::SUCCESS;
    }
}
