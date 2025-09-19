<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'property_id',
        'start_date',
        'end_date',
        'status',
        'rejection_reason',
        'deletion_reason',
        'deleted_by',
        'deleted_at',
        'total_price',
        'pricing_method',
        'global_pricing_id',
        'pricing_details',
        'special_requests',
        'payment_status',
        'amount_paid',
        'admin_notes',
        'approved_at',
        'approved_by',
        'paid_at',
        'created_by',
        'guests',
        'guest_name',
        'guest_email',
        'guest_phone',
        'is_guest_reservation',
        'guest_token'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_price' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'pricing_details' => 'array',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Relación con el precio global usado
     */
    public function globalPricing(): BelongsTo
    {
        return $this->belongsTo(GlobalPricing::class, 'global_pricing_id');
    }

    /**
     * Scope para reservas de huéspedes
     */
    public function scopeGuestReservations($query)
    {
        return $query->where('is_guest_reservation', true);
    }

    /**
     * Scope para reservas de usuarios registrados
     */
    public function scopeUserReservations($query)
    {
        return $query->where('is_guest_reservation', false);
    }

    /**
     * Obtener el nombre del cliente (usuario o huésped)
     */
    public function getCustomerNameAttribute()
    {
        return $this->is_guest_reservation ? $this->guest_name : $this->user->name;
    }

    /**
     * Obtener el email del cliente (usuario o huésped)
     */
    public function getCustomerEmailAttribute()
    {
        return $this->is_guest_reservation ? $this->guest_email : $this->user->email;
    }

    /**
     * Obtener el teléfono del cliente (usuario o huésped)
     */
    public function getCustomerPhoneAttribute()
    {
        return $this->is_guest_reservation ? $this->guest_phone : $this->user->phone;
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function getNightsAttribute()
    {
        if (!$this->start_date || !$this->end_date) {
            return 0;
        }
        return $this->start_date->diffInDays($this->end_date);
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => '<span class="badge bg-warning">Pendiente</span>',
            'approved' => '<span class="badge bg-success">Aprobada</span>',
            'rejected' => '<span class="badge bg-danger">Rechazada</span>',
            default => '<span class="badge bg-secondary">Desconocido</span>'
        };
    }

    /**
     * Alias para total_price para compatibilidad con la vista
     */
    public function getTotalAmountAttribute()
    {
        return $this->total_price;
    }

    public function canBeCancelled()
    {
        return $this->status === 'pending' || 
               ($this->status === 'approved' && $this->start_date->isFuture());
    }

    public function canBeDeleted()
    {
        // Verificar si el usuario tiene permiso para eliminar reservas
        return auth()->user()->hasPermission('delete_reservations');
    }

    /**
     * Accessor para check_in (alias de start_date)
     */
    public function getCheckInAttribute()
    {
        return $this->start_date;
    }

    /**
     * Accessor para check_out (alias de end_date)
     */
    public function getCheckOutAttribute()
    {
        return $this->end_date;
    }
}
