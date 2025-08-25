<?php

namespace App\Services;

use App\Models\Property;
use App\Models\Discount;
use App\Models\NightlyPrice;
use App\Models\ReservationDiscount;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PricingService
{
    /**
     * Calcular el precio total para una reserva
     */
    public function calculateReservationPrice(Property $property, Carbon $checkIn, Carbon $checkOut, array $options = []): array
    {
        $nights = $checkIn->diffInDays($checkOut);
        $totalPrice = 0;
        $nightlyPrices = [];
        $appliedDiscounts = [];

        // Calcular precio por noche
        for ($i = 0; $i < $nights; $i++) {
            $currentDate = $checkIn->copy()->addDays($i);
            $nightPrice = $this->getNightlyPrice($property, $currentDate);
            
            $nightlyPrices[] = [
                'date' => $currentDate->format('Y-m-d'),
                'price' => $nightPrice,
                'is_weekend' => $currentDate->isWeekend(),
                'is_holiday' => $this->isHoliday($currentDate),
                'is_seasonal' => $this->isSeasonalDate($currentDate)
            ];
            
            $totalPrice += $nightPrice;
        }

        // Aplicar descuentos automáticos
        $automaticDiscounts = $this->getAutomaticDiscounts($nights, $totalPrice, $checkIn);
        foreach ($automaticDiscounts as $discount) {
            $discountResult = $discount->applyToAmount($totalPrice);
            $totalPrice = $discountResult['final_amount'];
            
            $appliedDiscounts[] = [
                'discount' => $discount,
                'amount' => $discountResult['discount_amount'],
                'type' => 'automatic'
            ];
        }

        return [
            'nights' => $nights,
            'nightly_prices' => $nightlyPrices,
            'subtotal' => $totalPrice + array_sum(array_column($appliedDiscounts, 'amount')),
            'total_price' => $totalPrice,
            'applied_discounts' => $appliedDiscounts,
            'available_discounts' => $this->getAvailableDiscounts($nights, $totalPrice, $checkIn)
        ];
    }

    /**
     * Obtener el precio para una noche específica
     */
    public function getNightlyPrice(Property $property, Carbon $date): float
    {
        // Buscar precio específico de la propiedad
        $propertyPrice = NightlyPrice::forProperty($property->id)
            ->active()
            ->where(function ($query) use ($date) {
                $query->whereNull('valid_from')
                    ->orWhere('valid_from', '<=', $date);
            })
            ->where(function ($query) use ($date) {
                $query->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', $date);
            })
            ->first();

        if ($propertyPrice) {
            return $propertyPrice->getPriceForDate($date);
        }

        // Buscar precio global
        $globalPrice = NightlyPrice::global()
            ->active()
            ->where(function ($query) use ($date) {
                $query->whereNull('valid_from')
                    ->orWhere('valid_from', '<=', $date);
            })
            ->where(function ($query) use ($date) {
                $query->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', $date);
            })
            ->first();

        if ($globalPrice) {
            return $globalPrice->getPriceForDate($date);
        }

        // Precio por defecto de la propiedad
        return $property->price ?? 100000;
    }

    /**
     * Obtener descuentos automáticos aplicables
     */
    public function getAutomaticDiscounts(int $nights, float $totalAmount, Carbon $checkIn): Collection
    {
        return Discount::automatic()
            ->active()
            ->where(function ($query) use ($checkIn) {
                $query->whereNull('valid_from')
                    ->orWhere('valid_from', '<=', $checkIn);
            })
            ->where(function ($query) use ($checkIn) {
                $query->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', $checkIn);
            })
            ->get()
            ->filter(function ($discount) use ($nights, $totalAmount, $checkIn) {
                return $discount->isValidForReservation($nights, $totalAmount, $checkIn);
            });
    }

    /**
     * Obtener descuentos disponibles para aplicación manual
     */
    public function getAvailableDiscounts(int $nights, float $totalAmount, Carbon $checkIn): Collection
    {
        return Discount::manual()
            ->active()
            ->where(function ($query) use ($checkIn) {
                $query->whereNull('valid_from')
                    ->orWhere('valid_from', '<=', $checkIn);
            })
            ->where(function ($query) use ($checkIn) {
                $query->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', $checkIn);
            })
            ->get()
            ->filter(function ($discount) use ($nights, $totalAmount, $checkIn) {
                return $discount->isValidForReservation($nights, $totalAmount, $checkIn);
            });
    }

    /**
     * Aplicar descuento manual a una reserva
     */
    public function applyManualDiscount(int $reservationId, int $discountId, string $adminNotes = null): array
    {
        $discount = Discount::findOrFail($discountId);
        
        // Aquí se debería obtener la reserva y calcular el monto
        // Por ahora retornamos un ejemplo
        $originalAmount = 500000; // Este valor debería venir de la reserva
        
        $discountResult = $discount->applyToAmount($originalAmount);
        
        $reservationDiscount = ReservationDiscount::create([
            'reservation_id' => $reservationId,
            'discount_id' => $discountId,
            'original_amount' => $discountResult['original_amount'],
            'discount_amount' => $discountResult['discount_amount'],
            'final_amount' => $discountResult['final_amount'],
            'status' => 'approved',
            'admin_notes' => $adminNotes,
            'applied_by' => auth()->id(),
            'applied_at' => now()
        ]);

        return [
            'success' => true,
            'reservation_discount' => $reservationDiscount,
            'discount_amount' => $discountResult['discount_amount'],
            'final_amount' => $discountResult['final_amount']
        ];
    }

    /**
     * Verificar si es una fecha de temporada
     */
    private function isSeasonalDate(Carbon $date): bool
    {
        $month = $date->month;
        // Temporada alta: junio-agosto, diciembre-enero
        return ($month >= 6 && $month <= 8) || $month == 12 || $month == 1;
    }

    /**
     * Verificar si es un día festivo
     */
    private function isHoliday(Carbon $date): bool
    {
        // Aquí se podría integrar con una API de días festivos
        // Por ahora retornamos false
        return false;
    }

    /**
     * Obtener estadísticas de precios
     */
    public function getPricingStats(): array
    {
        $totalProperties = Property::count();
        $activePrices = NightlyPrice::active()->count();
        $activeDiscounts = Discount::active()->count();
        
        $averageBasePrice = NightlyPrice::active()
            ->whereNotNull('base_price')
            ->avg('base_price') ?? 0;

        return [
            'total_properties' => $totalProperties,
            'active_prices' => $activePrices,
            'active_discounts' => $activeDiscounts,
            'average_base_price' => round($averageBasePrice, 2)
        ];
    }
}
