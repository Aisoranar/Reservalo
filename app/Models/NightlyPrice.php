<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class NightlyPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'base_price',
        'weekend_price',
        'holiday_price',
        'seasonal_price',
        'valid_from',
        'valid_until',
        'is_global',
        'is_active',
        'notes'
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'weekend_price' => 'decimal:2',
        'holiday_price' => 'decimal:2',
        'seasonal_price' => 'decimal:2',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_global' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Obtener el precio para una fecha específica
     */
    public function getPriceForDate(Carbon $date): float
    {
        // Verificar si el precio está vigente
        if (!$this->isActiveForDate($date)) {
            return 0;
        }

        // Precio por temporada (si existe)
        if ($this->seasonal_price && $this->isSeasonalDate($date)) {
            return $this->seasonal_price;
        }

        // Precio para días festivos (si existe)
        if ($this->holiday_price && $this->isHoliday($date)) {
            return $this->holiday_price;
        }

        // Precio para fines de semana (si existe)
        if ($this->weekend_price && $date->isWeekend()) {
            return $this->weekend_price;
        }

        // Precio base
        return $this->base_price;
    }

    /**
     * Verificar si el precio está activo para una fecha
     */
    public function isActiveForDate(Carbon $date): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->valid_from && $date->lt($this->valid_from)) {
            return false;
        }

        if ($this->valid_until && $date->gt($this->valid_until)) {
            return false;
        }

        return true;
    }

    /**
     * Verificar si es una fecha de temporada (implementar lógica según necesidades)
     */
    private function isSeasonalDate(Carbon $date): bool
    {
        // Ejemplo: temporada alta en verano (junio-agosto)
        $month = $date->month;
        return $month >= 6 && $month <= 8;
    }

    /**
     * Verificar si es un día festivo (implementar lógica según necesidades)
     */
    private function isHoliday(Carbon $date): bool
    {
        // Aquí se podría integrar con una API de días festivos
        // Por ahora, retornamos false
        return false;
    }

    /**
     * Scope para precios activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para precios globales
     */
    public function scopeGlobal($query)
    {
        return $query->where('is_global', true);
    }

    /**
     * Scope para precios de una propiedad específica
     */
    public function scopeForProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }
}
