<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Membership;
use App\Models\User;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CheckExpiringMemberships extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'memberships:check-expiring {--days=7 : Días antes de expirar para notificar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar membresías próximas a expirar y enviar notificaciones';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $notificationDays = SystemSetting::get('membership_notification_days', 7);
        
        $this->info("Verificando membresías que expiran en {$days} días...");

        // Obtener membresías próximas a expirar
        $expiringMemberships = Membership::expiringSoon($days)
            ->with(['user', 'plan'])
            ->get();

        $notifiedCount = 0;

        foreach ($expiringMemberships as $membership) {
            // Verificar si ya se envió la notificación
            if ($membership->user->membership_notification_sent) {
                continue;
            }

            try {
                // Enviar notificación por email
                $this->sendExpirationNotification($membership);
                
                // Marcar como notificado
                $membership->user->update(['membership_notification_sent' => true]);
                
                $notifiedCount++;
                
                $this->line("Notificación enviada a: {$membership->user->email}");

            } catch (\Exception $e) {
                Log::error('Error al enviar notificación de membresía', [
                    'membership_id' => $membership->id,
                    'user_id' => $membership->user_id,
                    'error' => $e->getMessage()
                ]);
                
                $this->error("Error al notificar a {$membership->user->email}: " . $e->getMessage());
            }
        }

        $this->info("Proceso completado. Se notificaron {$notifiedCount} usuarios.");
        
        return Command::SUCCESS;
    }

    /**
     * Enviar notificación de expiración
     */
    private function sendExpirationNotification(Membership $membership)
    {
        $user = $membership->user;
        $plan = $membership->plan;
        $daysRemaining = $membership->days_remaining;

        // Aquí puedes implementar el envío de email
        // Por ahora solo logueamos la acción
        Log::info('Notificación de expiración de membresía', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'membership_id' => $membership->id,
            'plan_name' => $plan->name,
            'days_remaining' => $daysRemaining,
            'expires_at' => $membership->expires_at
        ]);

        // TODO: Implementar envío real de email
        // Mail::to($user->email)->send(new MembershipExpirationNotification($membership));
    }
}
