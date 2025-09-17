<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\MembershipPlan;
use App\Models\Membership;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener roles
        $superAdminRole = Role::where('name', 'superadmin')->first();
        $adminRole = Role::where('name', 'admin')->first();
        $userRole = Role::where('name', 'user')->first();

        // Obtener plan básico
        $basicPlan = MembershipPlan::where('slug', 'basic')->first();

        // 1. Super Administrador
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

        // Asignar rol de superadmin
        if ($superAdminRole) {
            $superAdmin->roles()->attach($superAdminRole->id, [
                'assigned_at' => now(),
                'assigned_by' => null
            ]);
        }

        // 2. Administrador
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

        // Asignar rol de admin
        if ($adminRole) {
            $admin->roles()->attach($adminRole->id, [
                'assigned_at' => now(),
                'assigned_by' => $superAdmin->id
            ]);
        }

        // 3. Usuarios regulares con diferentes tipos de cuenta
        $users = [
            [
                'name' => 'Juan Pérez',
                'email' => 'juan@example.com',
                'phone' => '+57 300 345 6789',
                'whatsapp' => '+57 300 345 6789',
                'address' => 'Calle 80 #12-34',
                'city' => 'Bogotá',
                'state' => 'Cundinamarca',
                'country' => 'Colombia',
                'postal_code' => '110221',
                'birth_date' => '1992-07-10',
                'gender' => 'male',
                'bio' => 'Propietario de apartamentos en Bogotá',
                'account_type' => 'premium',
            ],
            [
                'name' => 'María García',
                'email' => 'maria@example.com',
                'phone' => '+57 300 456 7890',
                'whatsapp' => '+57 300 456 7890',
                'address' => 'Carrera 50 #25-67',
                'city' => 'Cali',
                'state' => 'Valle del Cauca',
                'country' => 'Colombia',
                'postal_code' => '760001',
                'birth_date' => '1988-11-25',
                'gender' => 'female',
                'bio' => 'Host de casas rurales en el Valle del Cauca',
                'account_type' => 'regular',
            ],
            [
                'name' => 'Carlos Rodríguez',
                'email' => 'carlos@example.com',
                'phone' => '+57 300 567 8901',
                'whatsapp' => '+57 300 567 8901',
                'address' => 'Avenida 30 #45-89',
                'city' => 'Barranquilla',
                'state' => 'Atlántico',
                'country' => 'Colombia',
                'postal_code' => '080001',
                'birth_date' => '1995-04-15',
                'gender' => 'male',
                'bio' => 'Empresario del sector turístico',
                'account_type' => 'business',
            ],
            [
                'name' => 'Ana López',
                'email' => 'ana@example.com',
                'phone' => '+57 300 678 9012',
                'whatsapp' => '+57 300 678 9012',
                'address' => 'Calle 70 #15-30',
                'city' => 'Bucaramanga',
                'state' => 'Santander',
                'country' => 'Colombia',
                'postal_code' => '680001',
                'birth_date' => '1993-09-08',
                'gender' => 'female',
                'bio' => 'Gestora de propiedades turísticas',
                'account_type' => 'premium',
            ],
            [
                'name' => 'Luis Martínez',
                'email' => 'luis@example.com',
                'phone' => '+57 300 789 0123',
                'whatsapp' => '+57 300 789 0123',
                'address' => 'Carrera 25 #40-12',
                'city' => 'Pereira',
                'state' => 'Risaralda',
                'country' => 'Colombia',
                'postal_code' => '660001',
                'birth_date' => '1991-12-03',
                'gender' => 'male',
                'bio' => 'Inversionista en bienes raíces',
                'account_type' => 'regular',
            ]
        ];

        foreach ($users as $userData) {
            $user = User::create(array_merge($userData, [
                'password' => Hash::make('password'),
                'role' => 'user',
                'is_active' => true,
                'can_manage_system' => false,
                'can_manage_memberships' => false,
                'email_verified_at' => now(),
            ]));

            // Asignar rol de usuario
            if ($userRole) {
                $user->roles()->attach($userRole->id, [
                    'assigned_at' => now(),
                    'assigned_by' => $admin->id
                ]);
            }

            // Crear membresía básica para algunos usuarios
            if (in_array($user->email, ['juan@example.com', 'maria@example.com', 'carlos@example.com']) && $basicPlan) {
                $membership = Membership::createForUser($user, $basicPlan, [
                    'price_paid' => $basicPlan->price,
                    'currency' => $basicPlan->currency,
                    'notes' => 'Membresía inicial creada por el sistema'
                ]);

                // Actualizar usuario con membresía
                $user->update([
                    'current_membership_id' => $membership->id,
                    'membership_expires_at' => $membership->expires_at
                ]);
            }
        }

        // 4. Usuario de prueba sin membresía
        $userWithoutMembership = User::create([
            'name' => 'Usuario Prueba',
            'email' => 'prueba@example.com',
            'password' => Hash::make('password'),
            'phone' => '+57 300 890 1234',
            'whatsapp' => '+57 300 890 1234',
            'role' => 'user',
            'address' => 'Calle 100 #50-25',
            'city' => 'Bogotá',
            'state' => 'Cundinamarca',
            'country' => 'Colombia',
            'postal_code' => '110321',
            'birth_date' => '1994-06-18',
            'gender' => 'other',
            'bio' => 'Usuario de prueba sin membresía activa',
            'account_type' => 'regular',
            'is_active' => true,
            'can_manage_system' => false,
            'can_manage_memberships' => false,
            'email_verified_at' => now(),
        ]);

        // Asignar rol de usuario
        if ($userRole) {
            $userWithoutMembership->roles()->attach($userRole->id, [
                'assigned_at' => now(),
                'assigned_by' => $admin->id
            ]);
        }

        // 5. Usuario desactivado para pruebas
        $deactivatedUser = User::create([
            'name' => 'Usuario Desactivado',
            'email' => 'desactivado@example.com',
            'password' => Hash::make('password'),
            'phone' => '+57 300 901 2345',
            'whatsapp' => '+57 300 901 2345',
            'role' => 'user',
            'address' => 'Carrera 15 #30-45',
            'city' => 'Cartagena',
            'state' => 'Bolívar',
            'country' => 'Colombia',
            'postal_code' => '130001',
            'birth_date' => '1987-02-14',
            'gender' => 'female',
            'bio' => 'Usuario desactivado para pruebas',
            'account_type' => 'regular',
            'is_active' => false,
            'deactivated_at' => now()->subDays(5),
            'deactivation_reason' => 'user_request',
            'deactivation_notes' => 'Usuario solicitó desactivación temporal',
            'can_manage_system' => false,
            'can_manage_memberships' => false,
            'email_verified_at' => now(),
        ]);

        // Asignar rol de usuario
        if ($userRole) {
            $deactivatedUser->roles()->attach($userRole->id, [
                'assigned_at' => now()->subDays(10),
                'assigned_by' => $admin->id
            ]);
        }

        $this->command->info('✅ Usuarios creados exitosamente:');
        $this->command->line('🔑 Super Admin: superadmin@reservalo.com / password');
        $this->command->line('🔑 Admin: admin@reservalo.com / password');
        $this->command->line('👤 Usuarios: juan@example.com, maria@example.com, carlos@example.com, ana@example.com, luis@example.com / password');
        $this->command->line('👤 Prueba: prueba@example.com / password (sin membresía)');
        $this->command->line('👤 Desactivado: desactivado@example.com / password (cuenta desactivada)');
    }
}
