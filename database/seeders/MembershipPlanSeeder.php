<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MembershipPlan;

class MembershipPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Plan Básico',
                'slug' => 'basic',
                'description' => 'Plan ideal para usuarios que recién comienzan',
                'price' => 0,
                'currency' => 'COP',
                'duration_days' => 30,
                'features' => [
                    'Hasta 3 propiedades',
                    'Hasta 10 reservas por mes',
                    'Soporte por email',
                    'Panel básico de estadísticas'
                ],
                'is_active' => true,
                'is_default' => true,
                'max_properties' => 3,
                'max_reservations' => 10,
                'permissions' => [
                    'view_properties', 'create_properties', 'edit_properties',
                    'view_reservations', 'create_reservations', 'edit_reservations', 'cancel_reservations',
                    'view_profile', 'edit_profile'
                ]
            ],
            [
                'name' => 'Plan Premium',
                'slug' => 'premium',
                'description' => 'Plan avanzado con más funcionalidades',
                'price' => 50000,
                'currency' => 'COP',
                'duration_days' => 30,
                'features' => [
                    'Hasta 10 propiedades',
                    'Reservas ilimitadas',
                    'Soporte prioritario',
                    'Panel avanzado de estadísticas',
                    'Notificaciones por WhatsApp',
                    'Gestión de calendario'
                ],
                'is_active' => true,
                'is_default' => false,
                'max_properties' => 10,
                'max_reservations' => null,
                'permissions' => [
                    'view_properties', 'create_properties', 'edit_properties', 'delete_properties',
                    'view_reservations', 'create_reservations', 'edit_reservations', 'cancel_reservations',
                    'view_profile', 'edit_profile', 'view_reports'
                ]
            ],
            [
                'name' => 'Plan Empresarial',
                'slug' => 'business',
                'description' => 'Plan completo para empresas y negocios grandes',
                'price' => 150000,
                'currency' => 'COP',
                'duration_days' => 30,
                'features' => [
                    'Propiedades ilimitadas',
                    'Reservas ilimitadas',
                    'Soporte 24/7',
                    'Panel completo de estadísticas',
                    'Notificaciones por WhatsApp y SMS',
                    'Gestión avanzada de calendario',
                    'API de integración',
                    'Reportes personalizados',
                    'Gestión de múltiples usuarios'
                ],
                'is_active' => true,
                'is_default' => false,
                'max_properties' => null,
                'max_reservations' => null,
                'permissions' => [
                    'view_properties', 'create_properties', 'edit_properties', 'delete_properties',
                    'view_reservations', 'create_reservations', 'edit_reservations', 'cancel_reservations',
                    'view_profile', 'edit_profile', 'view_reports', 'view_advanced_reports', 'export_data'
                ]
            ],
            [
                'name' => 'Plan de Prueba',
                'slug' => 'trial',
                'description' => 'Plan de prueba por 7 días',
                'price' => 0,
                'currency' => 'COP',
                'duration_days' => 7,
                'features' => [
                    'Hasta 2 propiedades',
                    'Hasta 5 reservas',
                    'Acceso completo a funcionalidades',
                    'Soporte por email'
                ],
                'is_active' => true,
                'is_default' => false,
                'max_properties' => 2,
                'max_reservations' => 5,
                'permissions' => [
                    'view_properties', 'create_properties', 'edit_properties',
                    'view_reservations', 'create_reservations', 'edit_reservations', 'cancel_reservations',
                    'view_profile', 'edit_profile'
                ]
            ]
        ];

        foreach ($plans as $plan) {
            MembershipPlan::create($plan);
        }
    }
}
