<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Membership extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'membership_plan_id',
        'starts_at',
        'expires_at',
        'status',
        'price_paid',
        'currency',
        'notes',
        'cancelled_at',
        'cancellation_reason',
        'auto_renew'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'price_paid' => 'decimal:2',
        'auto_renew' => 'boolean'
    ];

    /**
     * Relaciones
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(MembershipPlan::class, 'membership_plan_id');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('expires_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')
                    ->orWhere('expires_at', '<=', now());
    }

    public function scopeExpiringSoon($query, $days = 7)
    {
        return $query->where('status', 'active')
                    ->where('expires_at', '<=', now()->addDays($days))
                    ->where('expires_at', '>', now());
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Verificar si la membresía está activa
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->expires_at > now();
    }

    /**
     * Verificar si la membresía ha expirado
     */
    public function isExpired(): bool
    {
        return $this->status === 'expired' || $this->expires_at <= now();
    }

    /**
     * Verificar si la membresía está próxima a expirar
     */
    public function isExpiringSoon(int $days = 7): bool
    {
        return $this->isActive() && $this->expires_at <= now()->addDays($days);
    }

    /**
     * Verificar si la membresía está cancelada
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Verificar si la membresía está suspendida
     */
    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    /**
     * Obtener días restantes
     */
    public function getDaysRemainingAttribute(): int
    {
        if ($this->isExpired()) {
            return 0;
        }
        return max(0, now()->diffInDays($this->expires_at, false));
    }

    /**
     * Obtener días transcurridos
     */
    public function getDaysElapsedAttribute(): int
    {
        return now()->diffInDays($this->starts_at);
    }

    /**
     * Obtener porcentaje de progreso
     */
    public function getProgressPercentageAttribute(): float
    {
        $totalDays = $this->starts_at->diffInDays($this->expires_at);
        $elapsedDays = $this->days_elapsed;
        
        if ($totalDays <= 0) {
            return 100;
        }
        
        return min(100, ($elapsedDays / $totalDays) * 100);
    }

    /**
     * Obtener precio formateado
     */
    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->price_paid, 0, ',', '.') . ' ' . $this->currency;
    }

    /**
     * Cancelar membresía
     */
    public function cancel(string $reason = null): bool
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $reason
        ]);

        return true;
    }

    /**
     * Suspender membresía
     */
    public function suspend(): bool
    {
        $this->update(['status' => 'suspended']);
        return true;
    }

    /**
     * Reactivar membresía
     */
    public function reactivate(): bool
    {
        $this->update(['status' => 'active']);
        return true;
    }

    /**
     * Extender membresía
     */
    public function extend(int $days): bool
    {
        $this->update([
            'expires_at' => $this->expires_at->addDays($days)
        ]);

        return true;
    }

    /**
     * Obtener membresías activas de un usuario
     */
    public static function getActiveForUser(int $userId): ?self
    {
        return static::active()->byUser($userId)->first();
    }

    /**
     * Obtener membresías que expiran pronto
     */
    public static function getExpiringSoon(int $days = 7): \Illuminate\Database\Eloquent\Collection
    {
        return static::expiringSoon($days)->get();
    }

    /**
     * Crear nueva membresía
     */
    public static function createForUser(User $user, MembershipPlan $plan, array $options = []): self
    {
        $startsAt = $options['starts_at'] ?? now();
        $expiresAt = $startsAt->copy()->addDays($plan->duration_days);

        return static::create([
            'user_id' => $user->id,
            'membership_plan_id' => $plan->id,
            'starts_at' => $startsAt,
            'expires_at' => $expiresAt,
            'price_paid' => $options['price_paid'] ?? $plan->price,
            'currency' => $options['currency'] ?? $plan->currency,
            'notes' => $options['notes'] ?? null,
            'auto_renew' => $options['auto_renew'] ?? false
        ]);
    }
}
