<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'message',
        'data',
        'user_id',
        'created_by',
        'related_id',
        'related_type',
        'is_read',
        'read_at',
        'is_urgent'
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'is_urgent' => 'boolean',
        'read_at' => 'datetime'
    ];

    /**
     * Relaciones
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function related(): MorphTo
    {
        return $this->morphTo('related', 'related_type', 'related_id');
    }

    /**
     * Scopes
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeUrgent($query)
    {
        return $query->where('is_urgent', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Marcar como leída
     */
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    /**
     * Marcar como no leída
     */
    public function markAsUnread(): void
    {
        $this->update([
            'is_read' => false,
            'read_at' => null
        ]);
    }

    /**
     * Crear notificación de aprobación de reserva
     */
    public static function createReservationApprovalNotification(
        int $userId,
        int $reservationId,
        string $reservationTitle,
        ?int $createdBy = null
    ): self {
        return static::create([
            'type' => 'reservation_approval',
            'title' => 'Nueva Reserva Requiere Aprobación',
            'message' => "Se ha creado una nueva reserva: {$reservationTitle}. Por favor, revisa y aprueba o rechaza la solicitud.",
            'data' => [
                'reservation_id' => $reservationId,
                'reservation_title' => $reservationTitle,
                'action_required' => 'approval'
            ],
            'user_id' => $userId,
            'created_by' => $createdBy,
            'related_id' => $reservationId,
            'related_type' => 'App\\Models\\Reservation',
            'is_urgent' => true
        ]);
    }

    /**
     * Crear notificación de sistema
     */
    public static function createSystemNotification(
        int $userId,
        string $title,
        string $message,
        array $data = [],
        bool $urgent = false,
        ?int $createdBy = null
    ): self {
        return static::create([
            'type' => 'system_alert',
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'user_id' => $userId,
            'created_by' => $createdBy,
            'is_urgent' => $urgent
        ]);
    }

    /**
     * Obtener notificaciones no leídas para un usuario
     */
    public static function getUnreadForUser(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return static::forUser($userId)
            ->unread()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Contar notificaciones no leídas para un usuario
     */
    public static function countUnreadForUser(int $userId): int
    {
        return static::forUser($userId)->unread()->count();
    }
}