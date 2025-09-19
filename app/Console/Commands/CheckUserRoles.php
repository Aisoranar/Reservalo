<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckUserRoles extends Command
{
    protected $signature = 'users:check-roles';
    protected $description = 'Verifica los roles de los usuarios y sus permisos';

    public function handle()
    {
        $this->info('🔍 Verificando roles de usuarios...');
        $this->newLine();

        $users = User::all();

        if ($users->isEmpty()) {
            $this->error('❌ No hay usuarios en el sistema');
            return Command::SUCCESS;
        }

        $this->info('👥 Usuarios encontrados:');
        $this->newLine();

        foreach ($users as $user) {
            $this->line("ID: {$user->id} | Email: {$user->email}");
            $this->line("   📋 Nombre: {$user->name}");
            $this->line("   🏷️ Rol: {$user->role}");
            $this->line("   ✅ Activo: " . ($user->is_active ? 'Sí' : 'No'));
            $this->line("   🔑 SuperAdmin: " . ($user->isSuperAdmin() ? 'Sí' : 'No'));
            $this->line("   🛡️ Admin: " . ($user->isAdmin() ? 'Sí' : 'No'));
            $this->line("   👤 Usuario: " . ($user->isUser() ? 'Sí' : 'No'));
            
            if ($user->roles()->count() > 0) {
                $this->line("   📋 Roles adicionales: " . $user->roles->pluck('name')->join(', '));
            }
            
            $this->newLine();
        }

        // Estadísticas
        $superAdmins = $users->where('role', 'superadmin')->count();
        $admins = $users->where('role', 'admin')->count();
        $regularUsers = $users->where('role', 'user')->count();
        $activeUsers = $users->where('is_active', true)->count();

        $this->info('📊 Estadísticas:');
        $this->line("   👑 SuperAdmins: {$superAdmins}");
        $this->line("   🛡️ Admins: {$admins}");
        $this->line("   👤 Usuarios regulares: {$regularUsers}");
        $this->line("   ✅ Usuarios activos: {$activeUsers}");
        $this->line("   ❌ Usuarios inactivos: " . ($users->count() - $activeUsers));

        return Command::SUCCESS;
    }
}