<?php

namespace App\Services;

use App\Models\Property;
use App\Models\Discount;
use App\Models\NightlyPrice;
use App\Models\GlobalPricing;
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
        // Obtener precio global activo primero
        $globalPricing = GlobalPricing::getActivePricing();
        $globalPrice = $globalPricing ? $globalPricing->final_price : 0;

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
            $specificPrice = $propertyPrice->getPriceForDate($date);
            
            // Si hay precio global activo y es mayor, usar el precio global
            if ($globalPrice > 0 && $globalPrice > $specificPrice) {
                return $globalPrice;
            }
            
            return $specificPrice;
        }

        // Si no hay precio específico, usar precio global
        if ($globalPrice > 0) {
            return $globalPrice;
        }

        // Buscar precio global en NightlyPrice (sistema anterior)
        $globalPriceOld = NightlyPrice::global()
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

        if ($globalPriceOld) {
            return $globalPriceOld->getPriceForDate($date);
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
     * Obtener el precio global activo
     */
    public function getActiveGlobalPricing(): ?GlobalPricing
    {
        return GlobalPricing::getActivePricing();
    }

    /**
     * Calcular precio usando precio global
     */
    public function calculatePriceWithGlobalPricing(Carbon $startDate, Carbon $endDate): array
    {
        $globalPricing = $this->getActiveGlobalPricing();
        
        if (!$globalPricing) {
            return [
                'success' => false,
                'message' => 'No hay precio global activo configurado'
            ];
        }

        $nights = $startDate->diffInDays($endDate);
        $basePrice = $globalPricing->final_price;
        $totalPrice = $basePrice * $nights;

        return [
            'success' => true,
            'global_pricing' => $globalPricing,
            'nights' => $nights,
            'base_price' => $basePrice,
            'total_price' => $totalPrice,
            'price_type' => $globalPricing->price_type
        ];
    }

    /**
     * Obtener estadísticas de precios
     */
    public function getPricingStats(): array
    {
        $totalProperties = Property::count();
        $activePrices = NightlyPrice::active()->count();
        $activeDiscounts = Discount::active()->count();
        $activeGlobalPricing = GlobalPricing::getActivePricing();
        
        $averageBasePrice = NightlyPrice::active()
            ->whereNotNull('base_price')
            ->avg('base_price') ?? 0;

        return [
            'total_properties' => $totalProperties,
            'active_prices' => $activePrices,
            'active_discounts' => $activeDiscounts,
            'average_base_price' => round($averageBasePrice, 2),
            'active_global_pricing' => $activeGlobalPricing ? [
                'name' => $activeGlobalPricing->name,
                'final_price' => $activeGlobalPricing->final_price,
                'price_type' => $activeGlobalPricing->price_type
            ] : null
        ];
    }
}
