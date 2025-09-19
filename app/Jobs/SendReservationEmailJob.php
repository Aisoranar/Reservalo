<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Reservation;
use App\Models\User;
use App\Services\TempAccountReservationService;
use Illuminate\Support\Facades\Log;

class SendReservationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $reservation;
    protected $tempUser;
    protected $emailType;

    /**
     * Create a new job instance.
     */
    public function __construct(Reservation $reservation, ?User $tempUser = null, string $emailType = 'reservation_created')
    {
        $this->reservation = $reservation;
        $this->tempUser = $tempUser;
        $this->emailType = $emailType;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $service = new TempAccountReservationService();
            $result = $service->sendReservationEmail($this->reservation, $this->tempUser);
            
            if (!$result['success']) {
                Log::error('Error en job de envío de correo de reserva', [
                    'reservation_id' => $this->reservation->id,
                    'error' => $result['message']
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error ejecutando job de envío de correo de reserva', [
                'reservation_id' => $this->reservation->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Job de envío de correo de reserva falló', [
            'reservation_id' => $this->reservation->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
