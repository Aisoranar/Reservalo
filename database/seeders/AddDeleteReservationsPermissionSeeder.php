<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class AddDeleteReservationsPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear el permiso de eliminación de reservas si no existe
        $permission = Permission::firstOrCreate(
            ['name' => 'delete_reservations'],
            [
                'display_name' => 'Eliminar Reservas',
                'description' => 'Eliminar reservas permanentemente',
                'category' => 'reservations'
            ]
        );

        // Agregar el permiso a los roles de admin y superadmin
        $adminRole = Role::where('name', 'admin')->first();
        $superAdminRole = Role::where('name', 'superadmin')->first();

        if ($adminRole && !$adminRole->hasPermission('delete_reservations')) {
            $adminRole->rolePermissions()->attach($permission->id);
        }

        if ($superAdminRole && !$superAdminRole->hasPermission('delete_reservations')) {
            $superAdminRole->rolePermissions()->attach($permission->id);
        }

        $this->command->info('Permiso de eliminación de reservas agregado correctamente');
    }
}