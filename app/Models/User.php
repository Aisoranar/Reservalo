<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'must_change_password',
        'temp_password',
        'deactivated_at',
        'deactivation_reason',
        'deactivation_notes',
        'reactivated_at',
        'last_login_at',
        'last_login_ip',
        'current_membership_id',
        'membership_expires_at',
        'membership_notification_sent',
        'can_manage_system',
        'can_manage_memberships',
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
        'must_change_password' => 'boolean',
        'membership_expires_at' => 'datetime',
        'membership_notification_sent' => 'boolean',
        'can_manage_system' => 'boolean',
        'can_manage_memberships' => 'boolean',
    ];

    /**
     * Relaciones
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Vincular reservas de huésped cuando el usuario se registra
     */
    public function linkGuestReservations()
    {
        // Buscar reservas de huésped con el mismo email
        $guestReservations = \App\Models\Reservation::where('is_guest_reservation', true)
            ->where('guest_email', $this->email)
            ->whereNull('user_id')
            ->get();

        foreach ($guestReservations as $reservation) {
            $reservation->update([
                'user_id' => $this->id,
                'is_guest_reservation' => false,
                'guest_token' => null // Limpiar el token ya que ahora está vinculado
            ]);
        }

        return $guestReservations->count();
    }

    public function propertyReviews(): HasMany
    {
        return $this->hasMany(PropertyReview::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')
                    ->withPivot('assigned_at', 'assigned_by')
                    ->withTimestamps();
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    public function currentMembership(): BelongsTo
    {
        return $this->belongsTo(Membership::class, 'current_membership_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function ownedProperties(): HasMany
    {
        return $this->hasMany(Property::class, 'owner_id');
    }

    /**
     * Verificar roles
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin' || $this->can_manage_system;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->role === 'superadmin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Verificar si tiene un rol específico
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role || $this->roles()->where('name', $role)->exists();
    }

    /**
     * Verificar si tiene alguno de los roles
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles) || $this->roles()->whereIn('name', $roles)->exists();
    }

    /**
     * Verificar si tiene un permiso específico
     */
    public function hasPermission(string $permission): bool
    {
        // Superadmin tiene todos los permisos
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Verificar permisos a través de roles
        return $this->roles()->whereHas('permissions', function ($query) use ($permission) {
            $query->where('name', $permission);
        })->exists();
    }

    /**
     * Verificar si tiene alguno de los permisos
     */
    public function hasAnyPermission(array $permissions): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Verificar si tiene todos los permisos
     */
    public function hasAllPermissions(array $permissions): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Asignar rol a usuario
     */
    public function assignRole(string $roleName): bool
    {
        $role = Role::where('name', $roleName)->first();
        
        if (!$role) {
            return false;
        }

        if (!$this->hasRole($roleName)) {
            $this->roles()->attach($role->id, [
                'assigned_at' => now(),
                'assigned_by' => auth()->id()
            ]);
        }

        return true;
    }

    /**
     * Remover rol de usuario
     */
    public function removeRole(string $roleName): bool
    {
        $role = Role::where('name', $roleName)->first();
        
        if (!$role) {
            return false;
        }

        $this->roles()->detach($role->id);
        return true;
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
     * Verificar si tiene membresía activa
     */
    public function hasActiveMembership(): bool
    {
        return $this->currentMembership && $this->currentMembership->isActive();
    }

    /**
     * Verificar si la membresía está próxima a expirar
     */
    public function isMembershipExpiringSoon(int $days = 7): bool
    {
        return $this->currentMembership && $this->currentMembership->isExpiringSoon($days);
    }

    /**
     * Obtener membresía activa
     */
    public function getActiveMembership(): ?Membership
    {
        return $this->currentMembership && $this->currentMembership->isActive() 
            ? $this->currentMembership 
            : null;
    }

    /**
     * Crear nueva membresía
     */
    public function createMembership(MembershipPlan $plan, array $options = []): Membership
    {
        $membership = Membership::createForUser($this, $plan, $options);
        
        // Actualizar usuario con nueva membresía
        $this->update([
            'current_membership_id' => $membership->id,
            'membership_expires_at' => $membership->expires_at,
            'membership_notification_sent' => false
        ]);

        return $membership;
    }

    /**
     * Extender membresía actual
     */
    public function extendMembership(int $days): bool
    {
        if (!$this->currentMembership) {
            return false;
        }

        $this->currentMembership->extend($days);
        
        $this->update([
            'membership_expires_at' => $this->currentMembership->expires_at
        ]);

        return true;
    }

    /**
     * Cancelar membresía actual
     */
    public function cancelMembership(string $reason = null): bool
    {
        if (!$this->currentMembership) {
            return false;
        }

        $this->currentMembership->cancel($reason);
        
        $this->update([
            'current_membership_id' => null,
            'membership_expires_at' => null
        ]);

        return true;
    }

    /**
     * Verificar si puede crear más propiedades según su plan
     */
    public function canCreateProperty(): bool
    {
        if (!$this->currentMembership) {
            return false;
        }

        $currentCount = $this->properties()->count();
        return $this->currentMembership->plan->allowsProperties($currentCount + 1);
    }

    /**
     * Verificar si puede crear más reservas según su plan
     */
    public function canCreateReservation(): bool
    {
        if (!$this->currentMembership) {
            return false;
        }

        $currentCount = $this->reservations()->count();
        return $this->currentMembership->plan->allowsReservations($currentCount + 1);
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
            'membership_status' => $this->hasActiveMembership() ? 'active' : 'inactive',
            'membership_expires_at' => $this->membership_expires_at?->format('d/m/Y'),
            'membership_days_remaining' => $this->currentMembership?->days_remaining ?? 0,
        ];
    }
}
