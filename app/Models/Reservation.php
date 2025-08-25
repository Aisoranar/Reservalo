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
        'total_price',
        'special_requests',
        'payment_status',
        'amount_paid',
        'admin_notes',
        'approved_at',
        'approved_by',
        'paid_at'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_price' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
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
}
