<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'is_active',
        'level',
        'permissions'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'permissions' => 'array'
    ];

    /**
     * Relaciones
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles')
                    ->withPivot('assigned_at', 'assigned_by')
                    ->withTimestamps();
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Verificar si el rol tiene un permiso específico
     */
    public function hasPermission(string $permission): bool
    {
        return $this->permissions()->where('name', $permission)->exists() ||
               in_array($permission, $this->permissions ?? []);
    }

    /**
     * Verificar si el rol tiene alguno de los permisos
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Verificar si el rol tiene todos los permisos
     */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Obtener roles por nivel
     */
    public static function getByLevel(int $level): \Illuminate\Database\Eloquent\Collection
    {
        return static::active()->byLevel($level)->get();
    }

    /**
     * Verificar si es superadmin
     */
    public function isSuperAdmin(): bool
    {
        return $this->name === 'superadmin' || $this->level >= 2;
    }

    /**
     * Verificar si es admin
     */
    public function isAdmin(): bool
    {
        return $this->name === 'admin' || $this->level >= 1;
    }

    /**
     * Verificar si es usuario regular
     */
    public function isUser(): bool
    {
        return $this->name === 'user' || $this->level === 0;
    }
}
