<?php

namespace App\Services;

use App\Models\User;
use App\Models\Reservation;
use App\Models\Property;
use App\Models\GlobalPricing;
use App\Models\AuditLog;
use App\Mail\ReservationNotification;
use App\Events\TempAccountCreated;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TempAccountReservationService
{
    /**
     * Crear una cuenta temporal para un cliente no registrado
     */
    public function createTempAccount(array $guestData): User
    {
        $tempPassword = $this->generateSecurePassword();
        
        $tempUser = User::create([
            'name' => $guestData['name'],
            'email' => $guestData['email'],
            'phone' => $guestData['phone'] ?? null,
            'password' => Hash::make($tempPassword),
            'role' => 'user',
            'account_type' => 'individual',
            'is_active' => true,
            'email_verified_at' => now(),
            'must_change_password' => true,
            'temp_password' => $tempPassword,
        ]);

        // Log de auditoría para creación de cuenta temporal
        AuditLog::log(
            'temp_account_created',
            'User',
            $tempUser->id,
            auth()->id() ?? 1,
            [
                'temp_user_email' => $tempUser->email,
                'created_for_reservation' => true
            ],
            [],
            'Cuenta temporal creada para reserva: ' . $tempUser->email
        );

        // Disparar evento
        event(new TempAccountCreated($tempUser, auth()->id() ?? 1));

        return $tempUser;
    }

    /**
     * Generar una contraseña temporal segura
     */
    private function generateSecurePassword(): string
    {
        // Generar contraseña con caracteres especiales para mayor seguridad
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        $password = '';
        
        for ($i = 0; $i < 12; $i++) {
            $password .= $characters[random_int(0, strlen($characters) - 1)];
        }
        
        return $password;
    }

    /**
     * Validar disponibilidad de la propiedad para las fechas seleccionadas
     */
    public function validatePropertyAvailability(int $propertyId, Carbon $startDate, Carbon $endDate): bool
    {
        $conflictingReservations = Reservation::where('property_id', $propertyId)
            ->where('status', '!=', 'rejected')
            ->where('status', '!=', 'deleted')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function ($q) use ($startDate, $endDate) {
                          $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                      });
            })
            ->exists();

        return !$conflictingReservations;
    }

    /**
     * Calcular precio basado en método seleccionado
     */
    public function calculatePrice(string $pricingMethod, ?int $globalPricingId, int $nights): array
    {
        if ($pricingMethod === 'global' && $globalPricingId) {
            $globalPricing = GlobalPricing::find($globalPricingId);
            
            if (!$globalPricing || !$globalPricing->is_active) {
                throw new \Exception('Precio global no válido o inactivo');
            }

            $pricePerUnit = $globalPricing->final_price;
            $totalPrice = $pricePerUnit * $nights;

            return [
                'method' => 'global',
                'global_pricing_id' => $globalPricing->id,
                'price_per_unit' => $pricePerUnit,
                'total_price' => $totalPrice,
                'pricing_details' => [
                    'global_pricing_name' => $globalPricing->name,
                    'base_price' => $globalPricing->base_price,
                    'final_price' => $globalPricing->final_price,
                    'price_type' => $globalPricing->price_type,
                    'has_discount' => $globalPricing->has_discount,
                    'discount_type' => $globalPricing->discount_type,
                    'discount_percentage' => $globalPricing->discount_percentage,
                    'discount_amount' => $globalPricing->discount_amount
                ]
            ];
        }

        // Para método manual, el precio se pasa desde el frontend
        return [
            'method' => 'manual',
            'global_pricing_id' => null,
            'price_per_unit' => null,
            'total_price' => null, // Se establecerá desde el request
            'pricing_details' => null
        ];
    }

    /**
     * Preparar datos del correo para reserva
     */
    public function prepareEmailData(Reservation $reservation, ?User $tempUser = null): array
    {
        $emailData = [
            'user_name' => $reservation->customer_name,
            'property_title' => $reservation->property->name,
            'property_location' => $reservation->property->location,
            'check_in_date' => $reservation->start_date ? $reservation->start_date->format('d/m/Y') : 'N/A',
            'check_in_time' => $reservation->start_date ? $reservation->start_date->format('H:i') : 'N/A',
            'check_out_date' => $reservation->end_date ? $reservation->end_date->format('d/m/Y') : 'N/A',
            'check_out_time' => $reservation->end_date ? $reservation->end_date->format('H:i') : 'N/A',
            'guests' => $reservation->guests,
            'nights' => $reservation->nights,
            'total_amount' => number_format($reservation->total_price, 0, ',', '.'),
            'admin_notes' => $reservation->admin_notes ?? '',
            'special_requests' => $reservation->special_requests ?? '',
            'is_guest_reservation' => $reservation->is_guest_reservation,
            'guest_token' => $reservation->guest_token
        ];

        // Si hay cuenta temporal, agregar información de acceso
        if ($tempUser) {
            $emailData['temp_account'] = true;
            $emailData['temp_email'] = $tempUser->email;
            $emailData['temp_password'] = $tempUser->temp_password;
            $emailData['login_url'] = route('login');
        }

        return $emailData;
    }

    /**
     * Enviar correo de notificación
     */
    public function sendReservationEmail(Reservation $reservation, ?User $tempUser = null, string $emailToSend = null): array
    {
        try {
            $emailData = $this->prepareEmailData($reservation, $tempUser);
            
            // Determinar tipo de plantilla
            $emailType = $tempUser ? 'reservation_created_temp_account' : 
                        ($reservation->status === 'approved' ? 'reservation_approved' : 'reservation_created');
            
            // Enviar correo - usar email especificado o el email del cliente
            $emailToUse = $emailToSend ?? $reservation->customer_email;
            Mail::to($emailToUse)->send(
                new ReservationNotification($emailType, $emailData)
            );

            // Log de auditoría
            AuditLog::log(
                'email_sent',
                'Reservation',
                $reservation->id,
                auth()->id() ?? 1,
                [
                    'email_type' => $emailType,
                    'user_email' => $reservation->customer_email,
                    'email_sent_to' => $emailToUse,
                    'temp_account' => $tempUser ? true : false
                ],
                [],
                'Correo de reserva enviado: ' . $emailType . ' a ' . $emailToUse
            );

            return [
                'success' => true,
                'message' => 'Correo enviado exitosamente',
                'email_type' => $emailType
            ];

        } catch (\Exception $e) {
            Log::error('Error enviando correo de reserva: ' . $e->getMessage(), [
                'reservation_id' => $reservation->id,
                'user_email' => $reservation->customer_email,
                'temp_user' => $tempUser ? $tempUser->id : null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Error enviando correo: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Crear reserva con cuenta temporal
     */
    public function createReservationWithTempAccount(array $reservationData, array $guestData, bool $sendEmail = true, string $emailToSend = null): array
    {
        try {
            // Validar disponibilidad
            if (!$this->validatePropertyAvailability(
                $reservationData['property_id'],
                Carbon::parse($reservationData['start_date']),
                Carbon::parse($reservationData['end_date'])
            )) {
                return [
                    'success' => false,
                    'message' => 'La propiedad no está disponible para las fechas seleccionadas'
                ];
            }

            // Crear cuenta temporal
            $tempUser = $this->createTempAccount($guestData);

            // Calcular precio si es necesario
            $pricingInfo = $this->calculatePrice(
                $reservationData['pricing_method'],
                $reservationData['global_pricing_id'] ?? null,
                $reservationData['nights'] ?? 1
            );

            // Preparar datos de la reserva
            $reservationData = array_merge($reservationData, [
                'user_id' => $tempUser->id,
                'is_guest_reservation' => false,
                'guest_name' => $guestData['name'],
                'guest_email' => $guestData['email'],
                'guest_phone' => $guestData['phone'] ?? null,
                'guest_token' => Str::random(32),
                'pricing_method' => $pricingInfo['method'],
                'global_pricing_id' => $pricingInfo['global_pricing_id'],
                'pricing_details' => $pricingInfo['pricing_details'] ? json_encode($pricingInfo['pricing_details']) : null
            ]);

            // Crear reserva
            $reservation = Reservation::create($reservationData);

            // Log de auditoría
            AuditLog::log(
                'manual_reservation_created_with_temp_account',
                'Reservation',
                $reservation->id,
                auth()->id(),
                [
                    'user_id' => $reservation->user_id,
                    'property_id' => $reservation->property_id,
                    'status' => $reservation->status,
                    'total_price' => $reservation->total_price,
                    'temp_account_created' => true
                ],
                [],
                'Reserva creada con cuenta temporal para: ' . $reservation->customer_name
            );

            // Enviar correo si está habilitado
            $emailResult = ['success' => true, 'message' => 'Correo no enviado'];
            if ($sendEmail) {
                // Usar el email especificado o el email del usuario temporal
                $emailToUse = $emailToSend ?? $tempUser->email;
                $emailResult = $this->sendReservationEmail($reservation, $tempUser, $emailToUse);
            }

            return [
                'success' => true,
                'reservation' => $reservation,
                'temp_user' => $tempUser,
                'email_result' => $emailResult,
                'message' => 'Reserva creada exitosamente' . 
                           ($emailResult['success'] ? ' y correo enviado' : ' pero error en correo: ' . $emailResult['message'])
            ];

        } catch (\Exception $e) {
            Log::error('Error creando reserva con cuenta temporal: ' . $e->getMessage(), [
                'reservation_data' => $reservationData,
                'guest_data' => $guestData,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Error creando reserva: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Limpiar cuentas temporales antiguas (para mantenimiento)
     */
    public function cleanupOldTempAccounts(int $daysOld = 30): int
    {
        $cutoffDate = now()->subDays($daysOld);
        
        $deletedCount = User::where('must_change_password', true)
            ->where('created_at', '<', $cutoffDate)
            ->whereDoesntHave('reservations')
            ->delete();

        if ($deletedCount > 0) {
            Log::info("Limpieza de cuentas temporales: {$deletedCount} cuentas eliminadas");
        }

        return $deletedCount;
    }
}
