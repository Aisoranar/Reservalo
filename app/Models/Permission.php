<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'category',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Relaciones
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Obtener permisos por categoría
     */
    public static function getByCategory(string $category): \Illuminate\Database\Eloquent\Collection
    {
        return static::active()->byCategory($category)->get();
    }

    /**
     * Obtener todas las categorías disponibles
     */
    public static function getCategories(): array
    {
        return static::active()
            ->distinct()
            ->pluck('category')
            ->toArray();
    }

    /**
     * Verificar si el permiso está activo
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Obtener permisos agrupados por categoría
     */
    public static function getGroupedByCategory(): \Illuminate\Support\Collection
    {
        return static::active()
            ->orderBy('category')
            ->orderBy('display_name')
            ->get()
            ->groupBy('category');
    }
}
