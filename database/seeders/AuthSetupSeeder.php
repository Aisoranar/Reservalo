<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Hash;

class AuthSetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar que el superadmin existe, si no, crearlo
        $superAdmin = User::where('email', 'superadmin@reservalo.com')->first();
        
        if (!$superAdmin) {
            $superAdmin = User::create([
                'name' => 'Super Administrador',
                'email' => 'superadmin@reservalo.com',
                'password' => Hash::make('password'),
                'phone' => '+57 300 123 4567',
                'whatsapp' => '+57 300 123 4567',
                'role' => 'superadmin',
                'address' => 'Calle 123 #45-67',
                'city' => 'Bogotá',
                'state' => 'Cundinamarca',
                'country' => 'Colombia',
                'postal_code' => '110111',
                'birth_date' => '1985-01-15',
                'gender' => 'male',
                'bio' => 'Super administrador del sistema Reservalo',
                'account_type' => 'business',
                'is_active' => true,
                'can_manage_system' => true,
                'can_manage_memberships' => true,
                'email_verified_at' => now(),
            ]);

            $this->command->info('✅ Super administrador creado: superadmin@reservalo.com / password');
        }

        // Verificar que el admin existe, si no, crearlo
        $admin = User::where('email', 'admin@reservalo.com')->first();
        
        if (!$admin) {
            $admin = User::create([
                'name' => 'Administrador',
                'email' => 'admin@reservalo.com',
                'password' => Hash::make('password'),
                'phone' => '+57 300 234 5678',
                'whatsapp' => '+57 300 234 5678',
                'role' => 'admin',
                'address' => 'Carrera 45 #78-90',
                'city' => 'Medellín',
                'state' => 'Antioquia',
                'country' => 'Colombia',
                'postal_code' => '050001',
                'birth_date' => '1990-03-20',
                'gender' => 'female',
                'bio' => 'Administradora del sistema Reservalo',
                'account_type' => 'business',
                'is_active' => true,
                'can_manage_system' => false,
                'can_manage_memberships' => false,
                'email_verified_at' => now(),
            ]);

            $this->command->info('✅ Administrador creado: admin@reservalo.com / password');
        }

        // Configurar sistema para permitir login
        SystemSetting::set('site_active', true, 'boolean', 'Estado del sitio', true);
        SystemSetting::set('maintenance_mode', false, 'boolean', 'Modo mantenimiento', true);
        SystemSetting::set('registration_enabled', true, 'boolean', 'Registro habilitado', true);
        SystemSetting::set('email_verification_required', false, 'boolean', 'Verificación de email requerida', true);

        $this->command->info('✅ Sistema configurado para permitir login');
        $this->command->info('🔐 Credenciales de acceso:');
        $this->command->line('   Super Admin: superadmin@reservalo.com / password');
        $this->command->line('   Admin: admin@reservalo.com / password');
        $this->command->line('   Usuarios: juan@example.com, maria@example.com, carlos@example.com / password');
    }
}
