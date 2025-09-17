<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\SystemSetting;

class GenerateAuditLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audit:generate-logs {--count=10 : Número de logs a generar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generar logs de auditoría de prueba para demostrar el sistema';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = $this->option('count');
        $user = User::where('email', 'superadmin@reservalo.com')->first();
        
        if (!$user) {
            $this->error('Usuario superadmin no encontrado');
            return 1;
        }

        $this->info("Generando {$count} logs de auditoría de prueba...");

        $actions = [
            'settings_updated' => 'Configuraciones del sistema actualizadas',
            'system_toggled' => 'Estado del sistema modificado',
            'maintenance_toggled' => 'Modo mantenimiento modificado',
            'user_created' => 'Usuario creado',
            'user_updated' => 'Usuario actualizado',
            'role_assigned' => 'Rol asignado a usuario',
            'membership_created' => 'Membresía creada',
            'membership_updated' => 'Membresía actualizada',
            'login' => 'Inicio de sesión',
            'logout' => 'Cierre de sesión'
        ];

        $models = ['User', 'Role', 'Permission', 'Membership', 'SystemSetting', 'Property', 'Reservation'];

        for ($i = 0; $i < $count; $i++) {
            $action = array_rand($actions);
            $model = $models[array_rand($models)];
            $description = $actions[$action];

            AuditLog::create([
                'user_id' => $user->id,
                'action' => $action,
                'model_type' => $model,
                'model_id' => rand(1, 100),
                'old_values' => ['test_field' => 'old_value_' . $i],
                'new_values' => ['test_field' => 'new_value_' . $i],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Test Browser)',
                'description' => $description . ' (Log de prueba #' . ($i + 1) . ')'
            ]);
        }

        $this->info("✅ {$count} logs de auditoría generados correctamente");
        $this->info("Puedes verlos en: /superadmin/audit-logs");

        return 0;
    }
}
