<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\NightlyPrice;
use App\Models\Property;
use Carbon\Carbon;

class NightlyPricesSeeder extends Seeder
{
    public function run(): void
    {
        // Verificar si ya existen precios nocturnos para evitar duplicados
        if (NightlyPrice::count() > 0) {
            $this->command->info('Los precios nocturnos ya existen. Saltando...');
            return;
        }

        // Obtener todas las propiedades existentes
        $properties = Property::all();

        if ($properties->count() > 0) {
            foreach ($properties as $property) {
                // Precio base para la propiedad
                NightlyPrice::create([
                    'property_id' => $property->id,
                    'base_price' => $property->price ?? 100000, // Usar el precio existente o $100,000 por defecto
                    'weekend_price' => null, // Se puede configurar después
                    'holiday_price' => null, // Se puede configurar después
                    'seasonal_price' => null, // Se puede configurar después
                    'valid_from' => Carbon::now()->startOfYear(),
                    'valid_until' => Carbon::now()->endOfYear()->addYear(),
                    'is_global' => false,
                    'is_active' => true,
                    'notes' => 'Precio base configurado automáticamente'
                ]);
            }
            $this->command->info('Precios nocturnos creados para ' . $properties->count() . ' propiedades');
        }

        // Crear precios globales por defecto
        $globalPrices = [
            [
                'property_id' => null,
                'base_price' => 80000, // $80,000 por defecto
                'weekend_price' => 95000, // $95,000 para fines de semana
                'holiday_price' => 120000, // $120,000 para días festivos
                'seasonal_price' => 110000, // $110,000 para temporada alta
                'valid_from' => Carbon::now()->startOfYear(),
                'valid_until' => Carbon::now()->endOfYear()->addYear(),
                'is_global' => true,
                'is_active' => true,
                'notes' => 'Precio global por defecto para nuevas propiedades'
            ],
            [
                'property_id' => null,
                'base_price' => 120000, // $120,000 para propiedades premium
                'weekend_price' => 140000, // $140,000 para fines de semana
                'holiday_price' => 180000, // $180,000 para días festivos
                'seasonal_price' => 160000, // $160,000 para temporada alta
                'valid_from' => Carbon::now()->startOfYear(),
                'valid_until' => Carbon::now()->endOfYear()->addYear(),
                'is_global' => true,
                'is_active' => true,
                'notes' => 'Precio global premium para propiedades de lujo'
            ],
            [
                'property_id' => null,
                'base_price' => 60000, // $60,000 para propiedades económicas
                'weekend_price' => 75000, // $75,000 para fines de semana
                'holiday_price' => 90000, // $90,000 para días festivos
                'seasonal_price' => 80000, // $80,000 para temporada alta
                'valid_from' => Carbon::now()->startOfYear(),
                'valid_until' => Carbon::now()->endOfYear()->addYear(),
                'is_global' => true,
                'is_active' => false,
                'notes' => 'Precio global económico para propiedades básicas'
            ],
            [
                'property_id' => null,
                'base_price' => 200000, // $200,000 para propiedades de lujo
                'weekend_price' => 250000, // $250,000 para fines de semana
                'holiday_price' => 300000, // $300,000 para días festivos
                'seasonal_price' => 280000, // $280,000 para temporada alta
                'valid_from' => Carbon::now()->startOfYear(),
                'valid_until' => Carbon::now()->endOfYear()->addYear(),
                'is_global' => true,
                'is_active' => false,
                'notes' => 'Precio global de lujo para propiedades exclusivas'
            ]
        ];

        foreach ($globalPrices as $priceData) {
            NightlyPrice::create($priceData);
        }

        $this->command->info('Precios nocturnos globales creados exitosamente: ' . count($globalPrices) . ' registros');
    }
}
