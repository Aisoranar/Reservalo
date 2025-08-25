<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Discount;

class DiscountsSeeder extends Seeder
{
    public function run(): void
    {
        $discounts = [
            [
                'name' => 'Descuento por estadía larga',
                'code' => 'LARGA_ESTADIA',
                'type' => 'percentage',
                'value' => 15.00, // 15% de descuento
                'application' => 'automatic',
                'min_nights' => 7, // Mínimo 7 noches
                'max_nights' => null,
                'min_amount' => 500.00, // Mínimo $500
                'description' => 'Descuento automático para reservas de 7 noches o más',
                'terms_conditions' => 'Válido para reservas confirmadas. No acumulable con otras promociones.'
            ],
            [
                'name' => 'Descuento por reserva anticipada',
                'code' => 'RESERVA_ANTICIPADA',
                'type' => 'percentage',
                'value' => 10.00, // 10% de descuento
                'application' => 'automatic',
                'min_nights' => 3,
                'max_nights' => null,
                'min_amount' => 300.00,
                'description' => 'Descuento por reservar con 30 días de anticipación',
                'terms_conditions' => 'Reserva debe realizarse al menos 30 días antes del check-in.'
            ],
            [
                'name' => 'Descuento por temporada baja',
                'code' => 'TEMPORADA_BAJA',
                'type' => 'percentage',
                'value' => 20.00, // 20% de descuento
                'application' => 'automatic',
                'min_nights' => 2,
                'max_nights' => null,
                'min_amount' => 200.00,
                'description' => 'Descuento automático en temporada baja (enero-febrero, septiembre-noviembre)',
                'terms_conditions' => 'Válido solo en temporada baja. No acumulable.'
            ],
            [
                'name' => 'Descuento por fidelidad',
                'code' => 'FIDELIDAD',
                'type' => 'percentage',
                'value' => 5.00, // 5% de descuento
                'application' => 'manual',
                'min_nights' => 1,
                'max_nights' => null,
                'min_amount' => 100.00,
                'description' => 'Descuento especial para clientes frecuentes',
                'terms_conditions' => 'Aplicado manualmente por el administrador. Requiere historial de reservas.'
            ],
            [
                'name' => 'Descuento por grupo',
                'code' => 'GRUPO',
                'type' => 'percentage',
                'value' => 12.00, // 12% de descuento
                'application' => 'manual',
                'min_nights' => 2,
                'max_nights' => null,
                'min_amount' => 800.00,
                'description' => 'Descuento para grupos de 4 o más personas',
                'terms_conditions' => 'Aplicado manualmente. Mínimo 4 personas por reserva.'
            ],
            [
                'name' => 'Descuento por último minuto',
                'code' => 'ULTIMO_MINUTO',
                'type' => 'percentage',
                'value' => 25.00, // 25% de descuento
                'application' => 'conditional',
                'min_nights' => 1,
                'max_nights' => 3,
                'min_amount' => 150.00,
                'description' => 'Descuento para reservas de último minuto (48h antes)',
                'terms_conditions' => 'Solo disponible 48 horas antes del check-in. No acumulable.'
            ],
            [
                'name' => 'Descuento por pago en efectivo',
                'code' => 'EFECTIVO',
                'type' => 'fixed_amount',
                'value' => 50.00, // $50 de descuento
                'application' => 'manual',
                'min_nights' => 2,
                'max_nights' => null,
                'min_amount' => 400.00,
                'description' => 'Descuento por pago en efectivo al momento del check-in',
                'terms_conditions' => 'Pago debe realizarse en efectivo. No válido para reservas online.'
            ],
            [
                'name' => 'Descuento por estadía extendida',
                'code' => 'EXTENDIDA',
                'type' => 'percentage',
                'value' => 18.00, // 18% de descuento
                'application' => 'automatic',
                'min_nights' => 14, // Mínimo 2 semanas
                'max_nights' => null,
                'min_amount' => 1000.00,
                'description' => 'Descuento para estadías de 2 semanas o más',
                'terms_conditions' => 'Válido para reservas de 14 noches o más. Descuento máximo aplicable.'
            ]
        ];

        foreach ($discounts as $discountData) {
            Discount::create($discountData);
        }
    }
}
