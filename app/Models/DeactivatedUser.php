<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class DeactivatedUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'birth_date',
        'gender',
        'bio',
        'profile_picture',
        'email_verified_at',
        'is_active',
        'account_type',
        'last_login_at',
        'last_login_ip',
        'deactivation_reason',
        'deactivation_notes',
        'deactivated_at',
        'deactivated_by',
        'reactivation_requested_at',
        'reactivation_reason',
        'deactivation_data',
        'deactivation_ip',
        'deactivation_user_agent',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'birth_date' => 'date',
        'last_login_at' => 'datetime',
        'deactivated_at' => 'datetime',
        'reactivation_requested_at' => 'datetime',
        'deactivation_data' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Scope para usuarios que pueden ser reactivados
     */
    public function scopeReactivatable($query)
    {
        return $query->where('is_active', false)
                    ->where('deactivation_reason', '!=', 'policy_violation')
                    ->where('deactivation_reason', '!=', 'suspicious_activity');
    }

    /**
     * Scope para usuarios desactivados por solicitud propia
     */
    public function scopeSelfDeactivated($query)
    {
        return $query->where('deactivation_reason', 'user_request');
    }

    /**
     * Scope para usuarios desactivados por inactividad
     */
    public function scopeInactive($query)
    {
        return $query->where('deactivation_reason', 'inactivity');
    }

    /**
     * Verificar si el usuario puede ser reactivado
     */
    public function canBeReactivated(): bool
    {
        return !in_array($this->deactivation_reason, ['policy_violation', 'suspicious_activity']);
    }

    /**
     * Verificar si el usuario ha solicitado reactivación
     */
    public function hasRequestedReactivation(): bool
    {
        return !is_null($this->reactivation_requested_at);
    }

    /**
     * Obtener el tiempo transcurrido desde la desactivación
     */
    public function getTimeSinceDeactivationAttribute(): string
    {
        return $this->deactivated_at->diffForHumans();
    }

    /**
     * Obtener el tiempo transcurrido desde la solicitud de reactivación
     */
    public function getTimeSinceReactivationRequestAttribute(): ?string
    {
        if (!$this->reactivation_requested_at) {
            return null;
        }
        return $this->reactivation_requested_at->diffForHumans();
    }

    /**
     * Obtener el motivo de desactivación en español
     */
    public function getDeactivationReasonTextAttribute(): string
    {
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
     * Relación con el usuario original (si existe)
     */
    public function originalUser()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

    /**
     * Relación con el admin que desactivó la cuenta (si aplica)
     */
    public function deactivatedByAdmin()
    {
        return $this->belongsTo(User::class, 'deactivated_by');
    }
}
