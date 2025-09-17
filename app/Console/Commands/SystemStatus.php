<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\MembershipPlan;
use App\Models\Membership;
use App\Models\SystemSetting;

class SystemStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show system status and statistics';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 SISTEMA RESERVALO - ESTADO ACTUAL');
        $this->line('=====================================');
        $this->line('');
        
        // Usuarios
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $superAdmins = User::where('role', 'superadmin')->count();
        $admins = User::where('role', 'admin')->count();
        $regularUsers = User::where('role', 'user')->count();
        
        $this->info('👥 USUARIOS:');
        $this->line("   Total: {$totalUsers}");
        $this->line("   Activos: {$activeUsers}");
        $this->line("   Super Admins: {$superAdmins}");
        $this->line("   Admins: {$admins}");
        $this->line("   Usuarios: {$regularUsers}");
        $this->line('');
        
        // Roles y Permisos
        $totalRoles = Role::count();
        $totalPermissions = Permission::count();
        
        $this->info('🔐 ROLES Y PERMISOS:');
        $this->line("   Roles: {$totalRoles}");
        $this->line("   Permisos: {$totalPermissions}");
        $this->line('');
        
        // Membresías
        $totalPlans = MembershipPlan::count();
        $activePlans = MembershipPlan::where('is_active', true)->count();
        $totalMemberships = Membership::count();
        $activeMemberships = Membership::where('status', 'active')->count();
        
        $this->info('💎 MEMBRESÍAS:');
        $this->line("   Planes: {$totalPlans} (Activos: {$activePlans})");
        $this->line("   Membresías: {$totalMemberships} (Activas: {$activeMemberships})");
        $this->line('');
        
        // Configuración del Sistema
        $siteActive = SystemSetting::get('site_active', false);
        $maintenanceMode = SystemSetting::get('maintenance_mode', false);
        $registrationEnabled = SystemSetting::get('registration_enabled', false);
        
        $this->info('⚙️ CONFIGURACIÓN:');
        $this->line("   Sitio Activo: " . ($siteActive ? '✅ Sí' : '❌ No'));
        $this->line("   Modo Mantenimiento: " . ($maintenanceMode ? '⚠️ Sí' : '✅ No'));
        $this->line("   Registro Habilitado: " . ($registrationEnabled ? '✅ Sí' : '❌ No'));
        $this->line('');
        
        // Credenciales de Acceso
        $this->info('🔑 CREDENCIALES DE ACCESO:');
        $this->line('   Super Admin: superadmin@reservalo.com / password');
        $this->line('   Admin: admin@reservalo.com / password');
        $this->line('   Usuarios: juan@example.com, maria@example.com, carlos@example.com / password');
        $this->line('   Prueba: prueba@example.com / password');
        $this->line('');
        
        // Comandos Disponibles
        $this->info('🛠️ COMANDOS DISPONIBLES:');
        $this->line('   php artisan users:list - Listar usuarios');
        $this->line('   php artisan auth:test {email} {password} - Probar login');
        $this->line('   php artisan system:status - Estado del sistema');
        $this->line('   php artisan memberships:check-expiring - Verificar membresías próximas a expirar');
        $this->line('   php artisan memberships:process-expired - Procesar membresías expiradas');
        $this->line('');
        
        $this->info('✅ Sistema completamente funcional y listo para usar!');
    }
}
