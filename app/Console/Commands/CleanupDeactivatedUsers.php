<?php

namespace App\Console\Commands;

use App\Services\UserDeactivationService;
use Illuminate\Console\Command;

class CleanupDeactivatedUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:cleanup-deactivated 
                            {--days=365 : Número de días después de los cuales limpiar usuarios desactivados}
                            {--dry-run : Mostrar qué se haría sin ejecutar la limpieza}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpia usuarios desactivados antiguos del sistema';

    protected $deactivationService;

    public function __construct(UserDeactivationService $deactivationService)
    {
        parent::__construct();
        $this->deactivationService = $deactivationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $dryRun = $this->option('dry-run');

        $this->info("🔍 Analizando usuarios desactivados...");
        
        // Obtener estadísticas
        $stats = $this->deactivationService->getDeactivationStats();
        
        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Total de usuarios desactivados', $stats['total']],
                ['Solicitudes de reactivación', $stats['reactivation_requests']],
                ['Pueden ser reactivados', $stats['can_be_reactivated']],
                ['Violaciones de políticas', $stats['policy_violations']],
                ['Actividad sospechosa', $stats['suspicious_activity']],
            ]
        );

        if ($dryRun) {
            $this->warn("🧪 MODO DRY-RUN: No se ejecutará ninguna acción");
        }

        $this->info("🗑️  Limpiando usuarios desactivados de más de {$days} días...");

        if ($dryRun) {
            // Simular la limpieza
            $this->info("📊 Se simularía la limpieza de usuarios desactivados antiguos");
            $this->info("💡 Usa --dry-run=false para ejecutar la limpieza real");
        } else {
            // Ejecutar la limpieza real
            $deletedCount = $this->deactivationService->cleanupOldDeactivatedUsers($days);
            
            if ($deletedCount > 0) {
                $this->info("✅ Se limpiaron {$deletedCount} usuarios desactivados antiguos");
            } else {
                $this->info("ℹ️  No se encontraron usuarios desactivados para limpiar");
            }
        }

        $this->info("🎯 Comando completado exitosamente");
        
        return Command::SUCCESS;
    }
}
