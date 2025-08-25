<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'whatsapp',
        'role',
        'password',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'birth_date',
        'gender',
        'bio',
        'profile_picture',
        'account_type',
        'is_active',
        'deactivated_at',
        'deactivation_reason',
        'deactivation_notes',
        'reactivated_at',
        'last_login_at',
        'last_login_ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'birth_date' => 'date',
        'last_login_at' => 'datetime',
        'deactivated_at' => 'datetime',
        'reactivated_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Relaciones
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function propertyReviews(): HasMany
    {
        return $this->hasMany(PropertyReview::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Verificar roles
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Verificar estado de la cuenta
     */
    public function isActive(): bool
    {
        return $this->is_active && is_null($this->deactivated_at);
    }

    public function isDeactivated(): bool
    {
        return !$this->is_active || !is_null($this->deactivated_at);
    }

    public function isSuspended(): bool
    {
        return in_array($this->deactivation_reason, ['policy_violation', 'suspicious_activity']);
    }

    /**
     * Obtener el tiempo transcurrido desde la desactivación
     */
    public function getTimeSinceDeactivationAttribute(): ?string
    {
        if (!$this->deactivated_at) {
            return null;
        }
        return $this->deactivated_at->diffForHumans();
    }

    /**
     * Obtener el tiempo transcurrido desde la reactivación
     */
    public function getTimeSinceReactivationAttribute(): ?string
    {
        if (!$this->reactivated_at) {
            return null;
        }
        return $this->reactivated_at->diffForHumans();
    }

    /**
     * Obtener el motivo de desactivación en español
     */
    public function getDeactivationReasonTextAttribute(): ?string
    {
        if (!$this->deactivation_reason) {
            return null;
        }

        $reasons = [
            'user_request' => 'Solicitud del usuario',
            'inactivity' => 'Inactividad',
            'policy_violation' => 'Violación de políticas',
            'suspicious_activity' => 'Actividad sospechosa',
            'temporary_hold' => 'Suspensión temporal',
            'other' => 'Otro motivo'
        ];

        return $reasons[$this->deactivation_reason] ?? 'Desconocido';
    }

    /**
     * Obtener el tipo de cuenta en español
     */
    public function getAccountTypeTextAttribute(): string
    {
        $types = [
            'regular' => 'Regular',
            'premium' => 'Premium',
            'business' => 'Empresarial'
        ];

        return $types[$this->account_type] ?? 'Regular';
    }

    /**
     * Scope para usuarios activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->whereNull('deactivated_at');
    }

    /**
     * Scope para usuarios desactivados
     */
    public function scopeDeactivated($query)
    {
        return $query->where('is_active', false)->orWhereNotNull('deactivated_at');
    }

    /**
     * Scope para usuarios suspendidos
     */
    public function scopeSuspended($query)
    {
        return $query->whereIn('deactivation_reason', ['policy_violation', 'suspicious_activity']);
    }

    /**
     * Actualizar último login
     */
    public function updateLastLogin(): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ]);
    }

    /**
     * Verificar si la cuenta puede ser reactivada
     */
    public function canBeReactivated(): bool
    {
        return !$this->isSuspended();
    }

    /**
     * Obtener estadísticas del usuario
     */
    public function getStatsAttribute(): array
    {
        return [
            'reservations_count' => $this->reservations()->count(),
            'reviews_count' => $this->propertyReviews()->count(),
            'account_age_days' => $this->created_at->diffInDays(now()),
            'last_activity' => $this->last_login_at?->diffForHumans() ?? 'Nunca',
        ];
    }
}
