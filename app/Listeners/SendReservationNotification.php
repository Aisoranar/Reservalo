<?php

namespace App\Listeners;

use App\Events\ReservationCreated;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendReservationNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ReservationCreated $event): void
    {
        $reservation = $event->reservation;
        
        // Obtener todos los usuarios con permisos para aprobar reservas
        $adminUsers = User::whereHas('roles', function($query) {
            $query->whereHas('rolePermissions', function($permissionQuery) {
                $permissionQuery->where('name', 'approve_reservations');
            });
        })->get();

        // Enviar notificación a cada administrador
        foreach ($adminUsers as $admin) {
            Notification::createReservationApprovalNotification(
                $admin->id,
                $reservation->id,
                "Reserva #{$reservation->id} - {$reservation->property->title}",
                $reservation->user_id
            );
        }
    }
}