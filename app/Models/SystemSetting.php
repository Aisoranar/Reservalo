<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
        'is_public'
    ];

    protected $casts = [
        'is_public' => 'boolean'
    ];

    /**
     * Scopes
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeByKey($query, $key)
    {
        return $query->where('key', $key);
    }

    /**
     * Obtener valor de configuración
     */
    public static function get(string $key, $default = null)
    {
        $cacheKey = "system_setting_{$key}";
        
        return Cache::remember($cacheKey, 3600, function () use ($key, $default) {
            $setting = static::byKey($key)->first();
            
            if (!$setting) {
                return $default;
            }
            
            return static::castValue($setting->value, $setting->type);
        });
    }

    /**
     * Establecer valor de configuración
     */
    public static function set(string $key, $value, string $type = 'string', string $description = null, bool $isPublic = false): self
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'description' => $description,
                'is_public' => $isPublic
            ]
        );

        // Limpiar cache
        Cache::forget("system_setting_{$key}");
        
        return $setting;
    }

    /**
     * Obtener múltiples configuraciones
     */
    public static function getMultiple(array $keys): array
    {
        $settings = static::whereIn('key', $keys)->get();
        $result = [];
        
        foreach ($settings as $setting) {
            $result[$setting->key] = static::castValue($setting->value, $setting->type);
        }
        
        return $result;
    }

    /**
     * Obtener todas las configuraciones públicas
     */
    public static function getPublicSettings(): array
    {
        return static::public()
            ->get()
            ->mapWithKeys(function ($setting) {
                return [$setting->key => static::castValue($setting->value, $setting->type)];
            })
            ->toArray();
    }

    /**
     * Convertir valor según el tipo
     */
    private static function castValue($value, string $type)
    {
        switch ($type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'json':
                return json_decode($value, true);
            case 'array':
                return is_string($value) ? json_decode($value, true) : $value;
            default:
                return $value;
        }
    }

    /**
     * Verificar si existe una configuración
     */
    public static function exists(string $key): bool
    {
        return static::byKey($key)->exists();
    }

    /**
     * Eliminar configuración
     */
    public static function remove(string $key): bool
    {
        $deleted = static::byKey($key)->delete();
        
        if ($deleted) {
            Cache::forget("system_setting_{$key}");
        }
        
        return $deleted > 0;
    }

    /**
     * Limpiar cache de todas las configuraciones
     */
    public static function clearCache(): void
    {
        $keys = static::pluck('key');
        
        foreach ($keys as $key) {
            Cache::forget("system_setting_{$key}");
        }
    }

    /**
     * Obtener configuraciones del sistema
     */
    public static function getSystemSettings(): array
    {
        return static::getMultiple([
            'site_name',
            'site_description',
            'site_active',
            'maintenance_mode',
            'registration_enabled',
            'email_verification_required',
            'max_properties_per_user',
            'max_reservations_per_user',
            'default_currency',
            'timezone',
            'date_format',
            'time_format'
        ]);
    }

    /**
     * Inicializar configuraciones por defecto
     */
    public static function initializeDefaults(): void
    {
        $defaults = [
            'site_name' => ['Reservalo', 'string', 'Nombre del sitio'],
            'site_description' => ['Sistema de reservas', 'string', 'Descripción del sitio'],
            'site_active' => [true, 'boolean', 'Estado del sitio'],
            'maintenance_mode' => [false, 'boolean', 'Modo mantenimiento'],
            'registration_enabled' => [true, 'boolean', 'Registro habilitado'],
            'email_verification_required' => [true, 'boolean', 'Verificación de email requerida'],
            'max_properties_per_user' => [10, 'integer', 'Máximo de propiedades por usuario'],
            'max_reservations_per_user' => [50, 'integer', 'Máximo de reservas por usuario'],
            'default_currency' => ['COP', 'string', 'Moneda por defecto'],
            'timezone' => ['America/Bogota', 'string', 'Zona horaria'],
            'date_format' => ['d/m/Y', 'string', 'Formato de fecha'],
            'time_format' => ['H:i', 'string', 'Formato de hora']
        ];

        foreach ($defaults as $key => [$value, $type, $description]) {
            if (!static::exists($key)) {
                static::set($key, $value, $type, $description, true);
            }
        }
    }
}
