<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GlobalPricing extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'base_price',
        'price_type',
        'has_discount',
        'discount_percentage',
        'discount_amount',
        'discount_type',
        'is_active',
        'description',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'has_discount' => 'boolean',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relación con el usuario que creó el precio
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relación con el usuario que actualizó el precio
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Calcular el precio final con descuento aplicado
     */
    public function getFinalPriceAttribute(): float
    {
        if (!$this->has_discount) {
            return $this->base_price;
        }

        if ($this->discount_type === 'percentage') {
            $discount = ($this->base_price * $this->discount_percentage) / 100;
            return $this->base_price - $discount;
        }

        if ($this->discount_type === 'fixed') {
            return max(0, $this->base_price - $this->discount_amount);
        }

        return $this->base_price;
    }

    /**
     * Obtener el precio activo actual (el más reciente)
     */
    public static function getActivePricing(): ?self
    {
        return self::where('is_active', true)->orderBy('updated_at', 'desc')->first();
    }

    /**
     * Obtener todos los precios activos
     */
    public static function getAllActivePricing(): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('is_active', true)->orderBy('updated_at', 'desc')->get();
    }

    /**
     * Scope para precios activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para precios por tipo
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('price_type', $type);
    }
}
