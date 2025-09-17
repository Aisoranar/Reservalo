<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'description'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array'
    ];

    /**
     * Relaciones
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scopes
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByModel($query, $modelType, $modelId = null)
    {
        $query = $query->where('model_type', $modelType);
        
        if ($modelId) {
            $query->where('model_id', $modelId);
        }
        
        return $query;
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Crear log de auditoría
     */
    public static function log(
        string $action,
        string $modelType,
        $modelId = null,
        $userId = null,
        array $oldValues = null,
        array $newValues = null,
        string $description = null
    ): self {
        return static::create([
            'user_id' => $userId ?? auth()->id(),
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'description' => $description
        ]);
    }

    /**
     * Obtener logs de un modelo específico
     */
    public static function getForModel(string $modelType, $modelId): \Illuminate\Database\Eloquent\Collection
    {
        return static::byModel($modelType, $modelId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtener logs de un usuario específico
     */
    public static function getForUser($userId, int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return static::byUser($userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtener logs recientes del sistema
     */
    public static function getRecent(int $days = 7, int $limit = 100): \Illuminate\Database\Eloquent\Collection
    {
        return static::recent($days)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtener estadísticas de acciones
     */
    public static function getActionStats(int $days = 30): \Illuminate\Support\Collection
    {
        return static::recent($days)
            ->selectRaw('action, COUNT(*) as count')
            ->groupBy('action')
            ->orderBy('count', 'desc')
            ->get();
    }

    /**
     * Obtener estadísticas por usuario
     */
    public static function getUserStats(int $days = 30): \Illuminate\Support\Collection
    {
        return static::recent($days)
            ->selectRaw('user_id, COUNT(*) as count')
            ->with('user:id,name,email')
            ->groupBy('user_id')
            ->orderBy('count', 'desc')
            ->get();
    }

    /**
     * Limpiar logs antiguos
     */
    public static function cleanup(int $days = 365): int
    {
        return static::where('created_at', '<', now()->subDays($days))->delete();
    }
}
