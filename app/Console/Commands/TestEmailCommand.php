<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReservationNotification;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {email=aisoranaya30@gmail.com}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enviar correo de prueba para verificar la configuración SMTP';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("🧪 Iniciando prueba de correo...");
        $this->info("📧 Enviando a: {$email}");
        
        try {
            // Enviar un correo simple de prueba sin depender de la base de datos
            Mail::raw('
¡Hola!

Este es un correo de prueba del sistema Reservalo.

Si recibes este mensaje, significa que la configuración SMTP está funcionando correctamente.

Detalles de la prueba:
- Fecha: ' . now()->format('d/m/Y H:i:s') . '
- Sistema: Reservalo
- Configuración: Gmail SMTP

¡El sistema de correos está funcionando perfectamente!

Saludos,
Equipo Reservalo
            ', function ($message) use ($email) {
                $message->to($email)
                        ->subject('🧪 Prueba de Correo - Reservalo')
                        ->from('admin@3coderslab.com', 'Reservalo');
            });

            $this->info("✅ ¡Correo enviado exitosamente!");
            $this->info("📬 Revisa la bandeja de entrada de: {$email}");
            $this->info("🔍 Si no lo ves, revisa la carpeta de spam");
            
            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error enviando correo:");
            $this->error($e->getMessage());
            
            // Mostrar detalles del error para debugging
            $this->warn("🔍 Detalles del error:");
            $this->warn("Archivo: " . $e->getFile());
            $this->warn("Línea: " . $e->getLine());
            
            return 1;
        }
    }
}
