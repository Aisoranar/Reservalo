<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GlobalPricing;
use App\Models\User;

class GlobalPricingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener el primer superadmin para asignar como creador
        $superadmin = User::where('role', 'superadmin')->first();

        // Verificar si ya existen precios globales para evitar duplicados
        if (GlobalPricing::count() > 0) {
            $this->command->info('Los precios globales ya existen. Saltando...');
            return;
        }

        $pricings = [
            // Precio estándar activo
            [
                'name' => 'Días Normales',
                'base_price' => 250000.00,
                'price_type' => 'daily',
                'has_discount' => false,
                'discount_percentage' => null,
                'discount_amount' => null,
                'discount_type' => null,
                'is_active' => true,
                'description' => 'Precio estándar por día para todas las propiedades del sistema',
                'created_by' => $superadmin ? $superadmin->id : null,
                'updated_by' => null
            ],
            // Precio promocional
            [
                'name' => 'Precio Promocional',
                'base_price' => 200000.00,
                'price_type' => 'daily',
                'has_discount' => true,
                'discount_percentage' => 15.00,
                'discount_amount' => null,
                'discount_type' => 'percentage',
                'is_active' => false,
                'description' => 'Precio promocional con 15% de descuento',
                'created_by' => $superadmin ? $superadmin->id : null,
                'updated_by' => null
            ],
            // Precio nocturno
            [
                'name' => 'Precio Nocturno',
                'base_price' => 100000.00,
                'price_type' => 'nightly',
                'has_discount' => false,
                'discount_percentage' => null,
                'discount_amount' => null,
                'discount_type' => null,
                'is_active' => false,
                'description' => 'Precio por noche para estadías cortas',
                'created_by' => $superadmin ? $superadmin->id : null,
                'updated_by' => null
            ],
            // Precio económico
            [
                'name' => 'Precio Económico',
                'base_price' => 80000.00,
                'price_type' => 'daily',
                'has_discount' => false,
                'discount_percentage' => null,
                'discount_amount' => null,
                'discount_type' => null,
                'is_active' => false,
                'description' => 'Precio económico para propiedades básicas',
                'created_by' => $superadmin ? $superadmin->id : null,
                'updated_by' => null
            ],
            // Precio premium
            [
                'name' => 'Precio Premium',
                'base_price' => 300000.00,
                'price_type' => 'daily',
                'has_discount' => true,
                'discount_percentage' => 10.00,
                'discount_amount' => null,
                'discount_type' => 'percentage',
                'is_active' => false,
                'description' => 'Precio premium para propiedades de lujo con 10% de descuento',
                'created_by' => $superadmin ? $superadmin->id : null,
                'updated_by' => null
            ],
            // Precio por semana (usando daily con descuento)
            [
                'name' => 'Precio Semanal',
                'base_price' => 900000.00,
                'price_type' => 'daily',
                'has_discount' => true,
                'discount_percentage' => 14.29, // Aproximadamente 1/7 de descuento
                'discount_amount' => null,
                'discount_type' => 'percentage',
                'is_active' => false,
                'description' => 'Precio por semana completa (7 días) con descuento',
                'created_by' => $superadmin ? $superadmin->id : null,
                'updated_by' => null
            ],
            // Precio por mes (usando daily con descuento)
            [
                'name' => 'Precio Mensual',
                'base_price' => 3000000.00,
                'price_type' => 'daily',
                'has_discount' => true,
                'discount_percentage' => 20.00,
                'discount_amount' => null,
                'discount_type' => 'percentage',
                'is_active' => false,
                'description' => 'Precio mensual con 20% de descuento para estadías largas',
                'created_by' => $superadmin ? $superadmin->id : null,
                'updated_by' => null
            ],
            // Precio con descuento fijo
            [
                'name' => 'Precio con Descuento Fijo',
                'base_price' => 180000.00,
                'price_type' => 'daily',
                'has_discount' => true,
                'discount_percentage' => null,
                'discount_amount' => 30000.00,
                'discount_type' => 'fixed',
                'is_active' => false,
                'description' => 'Precio con descuento fijo de $30,000',
                'created_by' => $superadmin ? $superadmin->id : null,
                'updated_by' => null
            ]
        ];

        foreach ($pricings as $pricingData) {
            GlobalPricing::create($pricingData);
        }

        $this->command->info('Precios globales creados exitosamente: ' . count($pricings) . ' registros');
    }
}