<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TestAdminAccess extends Command
{
    protected $signature = 'admin:test-access';
    protected $description = 'Prueba el acceso de administrador simulando autenticación';

    public function handle()
    {
        $this->info('🔍 Probando acceso de administrador...');
        $this->newLine();

        // Buscar usuario administrador
        $admin = User::where('email', 'admin@reservalo.com')->first();

        if (!$admin) {
            $this->error('❌ No se encontró el usuario administrador');
            return Command::FAILURE;
        }

        $this->info("✅ Usuario administrador encontrado: {$admin->name} ({$admin->email})");
        $this->line("   🏷️ Rol: {$admin->role}");
        $this->line("   ✅ Activo: " . ($admin->is_active ? 'Sí' : 'No'));
        $this->line("   🛡️ Es Admin: " . ($admin->isAdmin() ? 'Sí' : 'No'));
        $this->newLine();

        // Simular autenticación
        Auth::login($admin);
        $this->info('🔐 Usuario autenticado como administrador');
        $this->newLine();

        // Probar métodos de verificación de roles
        $this->info('🧪 Probando verificaciones de roles:');
        $this->line("   hasRole('admin'): " . ($admin->hasRole('admin') ? '✅' : '❌'));
        $this->line("   hasRole('superadmin'): " . ($admin->hasRole('superadmin') ? '✅' : '❌'));
        $this->line("   hasAnyRole(['admin']): " . ($admin->hasAnyRole(['admin']) ? '✅' : '❌'));
        $this->line("   hasAnyRole(['admin', 'superadmin']): " . ($admin->hasAnyRole(['admin', 'superadmin']) ? '✅' : '❌'));
        $this->line("   isAdmin(): " . ($admin->isAdmin() ? '✅' : '❌'));
        $this->line("   isSuperAdmin(): " . ($admin->isSuperAdmin() ? '✅' : '❌'));
        $this->newLine();

        // Probar acceso a rutas
        $this->info('🛣️ Probando acceso a rutas de administrador:');
        
        $adminRoutes = [
            'admin.dashboard' => 'Dashboard',
            'admin.reservations.index' => 'Reservas',
            'admin.users.index' => 'Usuarios',
            'admin.properties.index' => 'Propiedades',
            'admin.reports' => 'Reportes',
        ];

        foreach ($adminRoutes as $route => $name) {
            try {
                $url = route($route);
                $this->line("   ✅ {$name}: {$url}");
            } catch (\Exception $e) {
                $this->line("   ❌ {$name}: Error - " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info('✅ Prueba completada. El administrador debería tener acceso a todas las funcionalidades.');

        return Command::SUCCESS;
    }
}