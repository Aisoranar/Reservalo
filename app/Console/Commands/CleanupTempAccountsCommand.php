<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TempAccountReservationService;

class CleanupTempAccountsCommand extends Command
{
    protected $signature = 'reservations:cleanup-temp-accounts {--days=30 : Número de días para considerar una cuenta como antigua}';
    protected $description = 'Limpiar cuentas temporales antiguas que no tienen reservas asociadas';

    public function handle()
    {
        $days = $this->option('days');
        $this->info("🧹 Iniciando limpieza de cuentas temporales antiguas (más de {$days} días)...");

        $service = new TempAccountReservationService();
        $deletedCount = $service->cleanupOldTempAccounts($days);

        if ($deletedCount > 0) {
            $this->info("✅ Se eliminaron {$deletedCount} cuentas temporales antiguas.");
        } else {
            $this->info("ℹ️ No se encontraron cuentas temporales para eliminar.");
        }

        return Command::SUCCESS;
    }
}
