<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MembershipPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'currency',
        'duration_days',
        'features',
        'is_active',
        'is_default',
        'max_properties',
        'max_reservations',
        'permissions'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
        'permissions' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean'
    ];

    /**
     * Relaciones
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeByPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('price', [$minPrice, $maxPrice]);
    }

    /**
     * Verificar si el plan está activo
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Verificar si es el plan por defecto
     */
    public function isDefault(): bool
    {
        return $this->is_default;
    }

    /**
     * Obtener duración en formato legible
     */
    public function getDurationTextAttribute(): string
    {
        if ($this->duration_days < 30) {
            return $this->duration_days . ' días';
        } elseif ($this->duration_days < 365) {
            $months = round($this->duration_days / 30);
            return $months . ' mes' . ($months > 1 ? 'es' : '');
        } else {
            $years = round($this->duration_days / 365);
            return $years . ' año' . ($years > 1 ? 's' : '');
        }
    }

    /**
     * Obtener precio formateado
     */
    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->price, 0, ',', '.') . ' ' . $this->currency;
    }

    /**
     * Verificar si el plan tiene una característica específica
     */
    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features ?? []);
    }

    /**
     * Obtener planes activos ordenados por precio
     */
    public static function getActivePlans(): \Illuminate\Database\Eloquent\Collection
    {
        return static::active()->orderBy('price')->get();
    }

    /**
     * Obtener el plan por defecto
     */
    public static function getDefault(): ?self
    {
        return static::active()->default()->first();
    }

    /**
     * Verificar si el plan permite un número específico de propiedades
     */
    public function allowsProperties(int $count): bool
    {
        return is_null($this->max_properties) || $count <= $this->max_properties;
    }

    /**
     * Verificar si el plan permite un número específico de reservas
     */
    public function allowsReservations(int $count): bool
    {
        return is_null($this->max_reservations) || $count <= $this->max_reservations;
    }
}
