<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'value',
        'application',
        'min_nights',
        'max_nights',
        'min_amount',
        'valid_from',
        'valid_until',
        'is_active',
        'description',
        'terms_conditions'
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean'
    ];

    public function reservationDiscounts(): HasMany
    {
        return $this->hasMany(ReservationDiscount::class);
    }

    /**
     * Verificar si el descuento es válido para una reserva
     */
    public function isValidForReservation($nights, $totalAmount, Carbon $checkInDate = null): bool
    {
        if (!$this->is_active) {
            return false;
        }

        // Verificar fechas de validez
        if ($this->valid_from && $checkInDate && $checkInDate->lt($this->valid_from)) {
            return false;
        }

        if ($this->valid_until && $checkInDate && $checkInDate->gt($this->valid_until)) {
            return false;
        }

        // Verificar noches mínimas
        if ($this->min_nights && $nights < $this->min_nights) {
            return false;
        }

        // Verificar noches máximas
        if ($this->max_nights && $nights > $this->max_nights) {
            return false;
        }

        // Verificar monto mínimo
        if ($this->min_amount && $totalAmount < $this->min_amount) {
            return false;
        }

        return true;
    }

    /**
     * Calcular el monto del descuento
     */
    public function calculateDiscountAmount($originalAmount): float
    {
        if ($this->type === 'percentage') {
            return ($originalAmount * $this->value) / 100;
        }

        return $this->value;
    }

    /**
     * Aplicar el descuento a un monto
     */
    public function applyToAmount($originalAmount): array
    {
        $discountAmount = $this->calculateDiscountAmount($originalAmount);
        $finalAmount = $originalAmount - $discountAmount;

        return [
            'original_amount' => $originalAmount,
            'discount_amount' => $discountAmount,
            'final_amount' => max(0, $finalAmount)
        ];
    }

    /**
     * Scope para descuentos activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para descuentos automáticos
     */
    public function scopeAutomatic($query)
    {
        return $query->where('application', 'automatic');
    }

    /**
     * Scope para descuentos manuales
     */
    public function scopeManual($query)
    {
        return $query->where('application', 'manual');
    }

    /**
     * Scope para descuentos por porcentaje
     */
    public function scopePercentage($query)
    {
        return $query->where('type', 'percentage');
    }

    /**
     * Scope para descuentos por monto fijo
     */
    public function scopeFixedAmount($query)
    {
        return $query->where('type', 'fixed_amount');
    }
}
