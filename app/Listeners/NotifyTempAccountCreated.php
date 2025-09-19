<?php

namespace App\Listeners;

use App\Events\TempAccountCreated;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class NotifyTempAccountCreated implements ShouldQueue
{
    use InteractsWithQueue;

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
    public function handle(TempAccountCreated $event): void
    {
        try {
            // Crear notificación para el administrador que creó la cuenta
            Notification::create([
                'user_id' => $event->createdBy,
                'type' => 'temp_account_created',
                'title' => 'Cuenta Temporal Creada',
                'message' => "Se creó una cuenta temporal para {$event->user->name} ({$event->user->email})",
                'data' => [
                    'temp_user_id' => $event->user->id,
                    'temp_user_email' => $event->user->email,
                    'created_at' => $event->user->created_at->toISOString()
                ],
                'is_read' => false
            ]);

            Log::info('Notificación de cuenta temporal creada', [
                'temp_user_id' => $event->user->id,
                'created_by' => $event->createdBy
            ]);

        } catch (\Exception $e) {
            Log::error('Error creando notificación de cuenta temporal', [
                'temp_user_id' => $event->user->id,
                'created_by' => $event->createdBy,
                'error' => $e->getMessage()
            ]);
        }
    }
}
