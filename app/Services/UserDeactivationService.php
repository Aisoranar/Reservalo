<?php

namespace App\Services;

use App\Models\User;
use App\Models\DeactivatedUser;
use App\Models\Reservation;
use App\Models\PropertyReview;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class UserDeactivationService
{
    /**
     * Desactivar la cuenta de un usuario
     */
    public function deactivateUser(User $user, string $reason = 'user_request', ?string $notes = null, ?string $deactivatedBy = null): bool
    {
        try {
            DB::beginTransaction();

            // Crear registro en la tabla de usuarios desactivados
            $deactivatedUser = DeactivatedUser::create([
                'name' => $user->name,
                'email' => $user->email,
                'password' => $user->password,
                'phone' => $user->phone ?? null,
                'address' => $user->address ?? null,
                'city' => $user->city ?? null,
                'state' => $user->state ?? null,
                'country' => $user->country ?? null,
                'postal_code' => $user->postal_code ?? null,
                'birth_date' => $user->birth_date ?? null,
                'gender' => $user->gender ?? null,
                'bio' => $user->bio ?? null,
                'profile_picture' => $user->profile_picture ?? null,
                'email_verified_at' => $user->email_verified_at,
                'is_active' => false,
                'account_type' => $user->account_type ?? 'regular',
                'last_login_at' => $user->last_login_at ?? null,
                'last_login_ip' => $user->last_login_ip ?? null,
                'deactivation_reason' => $reason,
                'deactivation_notes' => $notes,
                'deactivated_at' => now(),
                'deactivated_by' => $deactivatedBy ?? 'self',
                'deactivation_data' => $this->collectUserData($user),
                'deactivation_ip' => request()->ip(),
                'deactivation_user_agent' => request()->userAgent(),
            ]);

            // Desactivar el usuario original (no eliminar)
            $user->update([
                'is_active' => false,
                'deactivated_at' => now(),
                'deactivation_reason' => $reason,
            ]);

            // Log de la acción
            Log::info('Usuario desactivado', [
                'user_id' => $user->id,
                'email' => $user->email,
                'reason' => $reason,
                'deactivated_by' => $deactivatedBy ?? 'self',
                'deactivated_user_id' => $deactivatedUser->id,
            ]);

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al desactivar usuario', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Reactivar la cuenta de un usuario
     */
    public function reactivateUser(DeactivatedUser $deactivatedUser, ?string $reactivationReason = null): bool
    {
        try {
            DB::beginTransaction();

            // Verificar si el usuario puede ser reactivado
            if (!$deactivatedUser->canBeReactivated()) {
                throw new \Exception('Este usuario no puede ser reactivado debido a: ' . $deactivatedUser->deactivation_reason_text);
            }

            // Buscar si existe un usuario activo con el mismo email
            $existingUser = User::where('email', $deactivatedUser->email)->first();

            if ($existingUser) {
                // Si existe, actualizar con los datos del usuario desactivado
                $existingUser->update([
                    'name' => $deactivatedUser->name,
                    'phone' => $deactivatedUser->phone,
                    'address' => $deactivatedUser->address,
                    'city' => $deactivatedUser->city,
                    'state' => $deactivatedUser->state,
                    'country' => $deactivatedUser->country,
                    'postal_code' => $deactivatedUser->postal_code,
                    'birth_date' => $deactivatedUser->birth_date,
                    'gender' => $deactivatedUser->gender,
                    'bio' => $deactivatedUser->bio,
                    'profile_picture' => $deactivatedUser->profile_picture,
                    'is_active' => true,
                    'deactivated_at' => null,
                    'deactivation_reason' => null,
                    'reactivated_at' => now(),
                ]);

                $user = $existingUser;
            } else {
                // Si no existe, crear un nuevo usuario
                $user = User::create([
                    'name' => $deactivatedUser->name,
                    'email' => $deactivatedUser->email,
                    'password' => $deactivatedUser->password,
                    'phone' => $deactivatedUser->phone,
                    'address' => $deactivatedUser->address,
                    'city' => $deactivatedUser->city,
                    'state' => $deactivatedUser->state,
                    'country' => $deactivatedUser->country,
                    'postal_code' => $deactivatedUser->postal_code,
                    'birth_date' => $deactivatedUser->birth_date,
                    'gender' => $deactivatedUser->gender,
                    'bio' => $deactivatedUser->bio,
                    'profile_picture' => $deactivatedUser->profile_picture,
                    'email_verified_at' => $deactivatedUser->email_verified_at,
                    'is_active' => true,
                    'account_type' => $deactivatedUser->account_type,
                    'last_login_at' => $deactivatedUser->last_login_at,
                    'last_login_ip' => $deactivatedUser->last_login_ip,
                    'reactivated_at' => now(),
                ]);
            }

            // Marcar el usuario desactivado como reactivado
            $deactivatedUser->update([
                'is_active' => true,
                'reactivation_requested_at' => now(),
                'reactivation_reason' => $reactivationReason,
            ]);

            // Log de la acción
            Log::info('Usuario reactivado', [
                'deactivated_user_id' => $deactivatedUser->id,
                'user_id' => $user->id,
                'email' => $user->email,
                'reactivation_reason' => $reactivationReason,
            ]);

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al reactivar usuario', [
                'deactivated_user_id' => $deactivatedUser->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Solicitar reactivación de cuenta
     */
    public function requestReactivation(DeactivatedUser $deactivatedUser, string $reason): bool
    {
        try {
            $deactivatedUser->update([
                'reactivation_requested_at' => now(),
                'reactivation_reason' => $reason,
            ]);

            // Aquí podrías enviar un email al admin o crear una notificación
            Log::info('Solicitud de reactivación recibida', [
                'deactivated_user_id' => $deactivatedUser->id,
                'email' => $deactivatedUser->email,
                'reason' => $reason,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Error al solicitar reactivación', [
                'deactivated_user_id' => $deactivatedUser->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Obtener estadísticas de usuarios desactivados
     */
    public function getDeactivationStats(): array
    {
        $total = DeactivatedUser::count();
        $selfDeactivated = DeactivatedUser::selfDeactivated()->count();
        $inactive = DeactivatedUser::inactive()->count();
        $policyViolations = DeactivatedUser::where('deactivation_reason', 'policy_violation')->count();
        $suspiciousActivity = DeactivatedUser::where('deactivation_reason', 'suspicious_activity')->count();
        $reactivationRequests = DeactivatedUser::whereNotNull('reactivation_requested_at')->count();

        return [
            'total' => $total,
            'self_deactivated' => $selfDeactivated,
            'inactive' => $inactive,
            'policy_violations' => $policyViolations,
            'suspicious_activity' => $suspiciousActivity,
            'reactivation_requests' => $reactivationRequests,
            'can_be_reactivated' => DeactivatedUser::reactivatable()->count(),
        ];
    }

    /**
     * Limpiar usuarios desactivados antiguos (opcional, para mantenimiento)
     */
    public function cleanupOldDeactivatedUsers(int $daysOld = 365): int
    {
        $cutoffDate = now()->subDays($daysOld);
        
        $deletedCount = DeactivatedUser::where('deactivated_at', '<', $cutoffDate)
            ->where('deactivation_reason', '!=', 'policy_violation')
            ->where('deactivation_reason', '!=', 'suspicious_activity')
            ->delete();

        Log::info('Limpieza de usuarios desactivados antiguos', [
            'deleted_count' => $deletedCount,
            'cutoff_date' => $cutoffDate->toDateString(),
        ]);

        return $deletedCount;
    }

    /**
     * Recolectar datos del usuario para el respaldo
     */
    private function collectUserData(User $user): array
    {
        $data = [
            'reservations_count' => $user->reservations()->count(),
            'reviews_count' => $user->propertyReviews()->count(),
            'favorites_count' => 0, // Implementar cuando tengas sistema de favoritos
            'last_activity' => $user->last_login_at?->toISOString(),
            'account_age_days' => $user->created_at->diffInDays(now()),
        ];

        // Datos de reservas
        $reservations = $user->reservations()
            ->with('property')
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($reservation) {
                return [
                    'id' => $reservation->id,
                    'property_name' => $reservation->property->name ?? 'N/A',
                    'start_date' => $reservation->start_date->toDateString(),
                    'end_date' => $reservation->end_date->toDateString(),
                    'status' => $reservation->status,
                    'total_price' => $reservation->total_price,
                ];
            });

        $data['recent_reservations'] = $reservations;

        // Datos de reseñas
        $reviews = $user->propertyReviews()
            ->with('property')
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($review) {
                return [
                    'id' => $review->id,
                    'property_name' => $review->property->name ?? 'N/A',
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'created_at' => $review->created_at->toDateString(),
                ];
            });

        $data['recent_reviews'] = $reviews;

        return $data;
    }
}
