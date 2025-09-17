<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear permisos
        $permissions = [
            // Permisos de sistema
            ['name' => 'manage_system', 'display_name' => 'Gestionar Sistema', 'description' => 'Control total del sistema', 'category' => 'system'],
            ['name' => 'manage_settings', 'display_name' => 'Gestionar Configuración', 'description' => 'Configurar parámetros del sistema', 'category' => 'system'],
            ['name' => 'view_audit_logs', 'display_name' => 'Ver Logs de Auditoría', 'description' => 'Acceder a logs del sistema', 'category' => 'system'],
            ['name' => 'manage_maintenance', 'display_name' => 'Gestionar Mantenimiento', 'description' => 'Activar/desactivar modo mantenimiento', 'category' => 'system'],
            
            // Permisos de usuarios
            ['name' => 'manage_users', 'display_name' => 'Gestionar Usuarios', 'description' => 'CRUD completo de usuarios', 'category' => 'users'],
            ['name' => 'view_users', 'display_name' => 'Ver Usuarios', 'description' => 'Ver lista de usuarios', 'category' => 'users'],
            ['name' => 'create_users', 'display_name' => 'Crear Usuarios', 'description' => 'Crear nuevos usuarios', 'category' => 'users'],
            ['name' => 'edit_users', 'display_name' => 'Editar Usuarios', 'description' => 'Modificar usuarios existentes', 'category' => 'users'],
            ['name' => 'delete_users', 'display_name' => 'Eliminar Usuarios', 'description' => 'Eliminar usuarios', 'category' => 'users'],
            ['name' => 'activate_users', 'display_name' => 'Activar Usuarios', 'description' => 'Activar/desactivar usuarios', 'category' => 'users'],
            ['name' => 'manage_user_roles', 'display_name' => 'Gestionar Roles de Usuario', 'description' => 'Asignar/remover roles', 'category' => 'users'],
            
            // Permisos de roles y permisos
            ['name' => 'manage_roles', 'display_name' => 'Gestionar Roles', 'description' => 'CRUD de roles', 'category' => 'roles'],
            ['name' => 'manage_permissions', 'display_name' => 'Gestionar Permisos', 'description' => 'CRUD de permisos', 'category' => 'roles'],
            ['name' => 'assign_permissions', 'display_name' => 'Asignar Permisos', 'description' => 'Asignar permisos a roles', 'category' => 'roles'],
            
            // Permisos de propiedades
            ['name' => 'manage_properties', 'display_name' => 'Gestionar Propiedades', 'description' => 'CRUD completo de propiedades', 'category' => 'properties'],
            ['name' => 'view_properties', 'display_name' => 'Ver Propiedades', 'description' => 'Ver lista de propiedades', 'category' => 'properties'],
            ['name' => 'create_properties', 'display_name' => 'Crear Propiedades', 'description' => 'Crear nuevas propiedades', 'category' => 'properties'],
            ['name' => 'edit_properties', 'display_name' => 'Editar Propiedades', 'description' => 'Modificar propiedades existentes', 'category' => 'properties'],
            ['name' => 'delete_properties', 'display_name' => 'Eliminar Propiedades', 'description' => 'Eliminar propiedades', 'category' => 'properties'],
            ['name' => 'approve_properties', 'display_name' => 'Aprobar Propiedades', 'description' => 'Aprobar/rechazar propiedades', 'category' => 'properties'],
            
            // Permisos de reservas
            ['name' => 'manage_reservations', 'display_name' => 'Gestionar Reservas', 'description' => 'CRUD completo de reservas', 'category' => 'reservations'],
            ['name' => 'view_reservations', 'display_name' => 'Ver Reservas', 'description' => 'Ver lista de reservas', 'category' => 'reservations'],
            ['name' => 'create_reservations', 'display_name' => 'Crear Reservas', 'description' => 'Crear nuevas reservas', 'category' => 'reservations'],
            ['name' => 'edit_reservations', 'display_name' => 'Editar Reservas', 'description' => 'Modificar reservas existentes', 'category' => 'reservations'],
            ['name' => 'cancel_reservations', 'display_name' => 'Cancelar Reservas', 'description' => 'Cancelar reservas', 'category' => 'reservations'],
            ['name' => 'approve_reservations', 'display_name' => 'Aprobar Reservas', 'description' => 'Aprobar/rechazar reservas', 'category' => 'reservations'],
            
            // Permisos de membresías
            ['name' => 'manage_memberships', 'display_name' => 'Gestionar Membresías', 'description' => 'CRUD completo de membresías', 'category' => 'memberships'],
            ['name' => 'manage_membership_plans', 'display_name' => 'Gestionar Planes de Membresía', 'description' => 'CRUD de planes de membresía', 'category' => 'memberships'],
            ['name' => 'view_memberships', 'display_name' => 'Ver Membresías', 'description' => 'Ver lista de membresías', 'category' => 'memberships'],
            ['name' => 'create_memberships', 'display_name' => 'Crear Membresías', 'description' => 'Crear nuevas membresías', 'category' => 'memberships'],
            ['name' => 'extend_memberships', 'display_name' => 'Extender Membresías', 'description' => 'Extender membresías existentes', 'category' => 'memberships'],
            ['name' => 'cancel_memberships', 'display_name' => 'Cancelar Membresías', 'description' => 'Cancelar membresías', 'category' => 'memberships'],
            
            // Permisos de reportes
            ['name' => 'view_reports', 'display_name' => 'Ver Reportes', 'description' => 'Acceder a reportes básicos', 'category' => 'reports'],
            ['name' => 'view_advanced_reports', 'display_name' => 'Ver Reportes Avanzados', 'description' => 'Acceder a reportes detallados', 'category' => 'reports'],
            ['name' => 'export_data', 'display_name' => 'Exportar Datos', 'description' => 'Exportar datos del sistema', 'category' => 'reports'],
            
            // Permisos de perfil
            ['name' => 'view_profile', 'display_name' => 'Ver Perfil', 'description' => 'Ver perfil propio', 'category' => 'profile'],
            ['name' => 'edit_profile', 'display_name' => 'Editar Perfil', 'description' => 'Modificar perfil propio', 'category' => 'profile'],
            ['name' => 'change_password', 'display_name' => 'Cambiar Contraseña', 'description' => 'Cambiar contraseña propia', 'category' => 'profile'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // Crear roles
        $roles = [
            [
                'name' => 'superadmin',
                'display_name' => 'Super Administrador',
                'description' => 'Control total del sistema',
                'level' => 2,
                'permissions' => ['*'] // Todos los permisos
            ],
            [
                'name' => 'admin',
                'display_name' => 'Administrador',
                'description' => 'Administración del sistema',
                'level' => 1,
                'permissions' => [
                    'manage_users', 'view_users', 'create_users', 'edit_users', 'activate_users',
                    'manage_properties', 'view_properties', 'create_properties', 'edit_properties', 'approve_properties',
                    'manage_reservations', 'view_reservations', 'create_reservations', 'edit_reservations', 'approve_reservations',
                    'manage_memberships', 'view_memberships', 'create_memberships', 'extend_memberships', 'cancel_memberships',
                    'view_reports', 'export_data',
                    'view_profile', 'edit_profile', 'change_password'
                ]
            ],
            [
                'name' => 'user',
                'display_name' => 'Usuario',
                'description' => 'Usuario regular del sistema',
                'level' => 0,
                'permissions' => [
                    'view_properties', 'create_properties', 'edit_properties',
                    'view_reservations', 'create_reservations', 'edit_reservations', 'cancel_reservations',
                    'view_memberships', 'create_memberships', 'extend_memberships', 'cancel_memberships',
                    'view_profile', 'edit_profile', 'change_password'
                ]
            ]
        ];

        foreach ($roles as $roleData) {
            $role = Role::create([
                'name' => $roleData['name'],
                'display_name' => $roleData['display_name'],
                'description' => $roleData['description'],
                'level' => $roleData['level'],
                'permissions' => $roleData['permissions']
            ]);

            // Asignar permisos al rol
            if ($roleData['permissions'][0] !== '*') {
                $permissionIds = Permission::whereIn('name', $roleData['permissions'])->pluck('id');
                $role->permissions()->attach($permissionIds);
            } else {
                // Superadmin tiene todos los permisos
                $role->permissions()->attach(Permission::pluck('id'));
            }
        }
    }
}
