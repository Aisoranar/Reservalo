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

        // Crear precio global por defecto
        GlobalPricing::create([
            'name' => 'Precio Estándar 2025',
            'base_price' => 150000.00,
            'price_type' => 'daily',
            'has_discount' => false,
            'discount_percentage' => null,
            'discount_amount' => null,
            'discount_type' => null,
            'is_active' => true,
            'description' => 'Precio estándar por día para todas las propiedades del sistema',
            'created_by' => $superadmin ? $superadmin->id : null,
            'updated_by' => null
        ]);

        // Crear precio con descuento de ejemplo
        GlobalPricing::create([
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
        ]);

        // Crear precio nocturno de ejemplo
        GlobalPricing::create([
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
        ]);
    }
}