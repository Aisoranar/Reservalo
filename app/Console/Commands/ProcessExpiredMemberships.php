<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Membership;
use App\Models\User;
use App\Models\SystemSetting;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Log;

class ProcessExpiredMemberships extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'memberships:process-expired {--grace-period=3 : Días de gracia después de expirar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Procesar membresías expiradas y aplicar restricciones';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $gracePeriod = $this->option('grace-period');
        $systemGracePeriod = SystemSetting::get('membership_grace_period', 3);
        
        $this->info("Procesando membresías expiradas con período de gracia de {$gracePeriod} días...");

        // Obtener membresías expiradas
        $expiredMemberships = Membership::where('status', 'active')
            ->where('expires_at', '<=', now()->subDays($gracePeriod))
            ->with(['user', 'plan'])
            ->get();

        $processedCount = 0;

        foreach ($expiredMemberships as $membership) {
            try {
                // Marcar membresía como expirada
                $membership->update(['status' => 'expired']);
                
                // Actualizar usuario
                $membership->user->update([
                    'current_membership_id' => null,
                    'membership_expires_at' => null,
                    'membership_notification_sent' => false
                ]);

                // Log de auditoría
                AuditLog::log(
                    'expired',
                    'Membership',
                    $membership->id,
                    null, // Sistema
                    ['status' => 'active'],
                    ['status' => 'expired'],
                    'Membresía expirada automáticamente'
                );

                $processedCount++;
                
                $this->line("Membresía expirada para: {$membership->user->email}");

            } catch (\Exception $e) {
                Log::error('Error al procesar membresía expirada', [
                    'membership_id' => $membership->id,
                    'user_id' => $membership->user_id,
                    'error' => $e->getMessage()
                ]);
                
                $this->error("Error al procesar membresía de {$membership->user->email}: " . $e->getMessage());
            }
        }

        // Procesar usuarios sin membresía activa
        $this->processUsersWithoutActiveMembership();

        $this->info("Proceso completado. Se procesaron {$processedCount} membresías expiradas.");
        
        return Command::SUCCESS;
    }

    /**
     * Procesar usuarios sin membresía activa
     */
    private function processUsersWithoutActiveMembership()
    {
        $this->info("Verificando usuarios sin membresía activa...");

        $usersWithoutMembership = User::whereDoesntHave('currentMembership', function($query) {
            $query->where('status', 'active')->where('expires_at', '>', now());
        })
        ->where('role', 'user') // Solo usuarios regulares
        ->get();

        foreach ($usersWithoutMembership as $user) {
            // Aquí puedes implementar lógica adicional para usuarios sin membresía
            // Por ejemplo, restringir funcionalidades, enviar notificaciones, etc.
            
            Log::info('Usuario sin membresía activa detectado', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'last_login' => $user->last_login_at
            ]);
        }

        $this->line("Se verificaron " . $usersWithoutMembership->count() . " usuarios sin membresía activa.");
    }
}
