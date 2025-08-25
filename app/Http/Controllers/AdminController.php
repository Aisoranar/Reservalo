<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DeactivatedUser;
use App\Models\Property;
use App\Models\Reservation;
use App\Services\UserDeactivationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    protected $deactivationService;

    public function __construct(UserDeactivationService $deactivationService)
    {
        $this->deactivationService = $deactivationService;
    }

    public function dashboard()
    {
        $stats = [
            'total_users' => User::active()->count(),
            'total_properties' => Property::count(),
            'total_reservations' => Reservation::count(),
            'pending_reservations' => Reservation::where('status', 'pending')->count(),
            'deactivated_users' => DeactivatedUser::count(),
            'reactivation_requests' => DeactivatedUser::where('reactivation_requested', true)->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    public function properties()
    {
        $properties = Property::with(['images', 'owner'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.properties.index', compact('properties'));
    }

    public function reservations()
    {
        $reservations = Reservation::with(['user', 'property'])
            ->latest()
            ->paginate(15);

        return view('admin.reservations.index', compact('reservations'));
    }

    public function users()
    {
        $users = User::active()
            ->with(['reservations', 'propertyReviews'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Panel de gestión de usuarios desactivados
     */
    public function deactivatedUsers(Request $request)
    {
        $query = DeactivatedUser::query();

        // Filtros
        if ($request->filled('reason')) {
            $query->where('deactivation_reason', $request->reason);
        }

        if ($request->filled('status')) {
            if ($request->status === 'can_reactivate') {
                $query->reactivatable();
            } elseif ($request->status === 'suspended') {
                $query->whereIn('deactivation_reason', ['policy_violation', 'suspicious_activity']);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Ordenamiento
        $orderBy = $request->get('order_by', 'deactivated_at');
        $orderDirection = $request->get('order_direction', 'desc');
        $query->orderBy($orderBy, $orderDirection);

        $deactivatedUsers = $query->paginate(20);
        $stats = $this->deactivationService->getDeactivationStats();

        // Agrupar por email para mostrar cuentas múltiples
        $groupedUsers = DeactivatedUser::select('email')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('email')
            ->having('count', '>', 1)
            ->get();

        return view('admin.deactivated-users.index', compact('deactivatedUsers', 'stats', 'groupedUsers'));
    }

    /**
     * Ver detalles de un usuario desactivado
     */
    public function showDeactivatedUser(DeactivatedUser $deactivatedUser)
    {
        // Buscar todas las instancias de este usuario (cuentas múltiples)
        $multipleAccounts = DeactivatedUser::where('email', $deactivatedUser->email)
            ->orderBy('deactivated_at', 'desc')
            ->get();

        // Obtener estadísticas del usuario
        $userStats = [
            'total_deactivations' => $multipleAccounts->count(),
            'first_deactivation' => $multipleAccounts->last()->deactivated_at,
            'last_deactivation' => $multipleAccounts->first()->deactivated_at,
            'total_reservations' => $deactivatedUser->deactivation_data['reservations_count'] ?? 0,
            'total_reviews' => $deactivatedUser->deactivation_data['reviews_count'] ?? 0,
        ];

        return view('admin.deactivated-users.show', compact('deactivatedUser', 'multipleAccounts', 'userStats'));
    }

    /**
     * Reactivar un usuario desactivado
     */
    public function reactivateUser(Request $request, DeactivatedUser $deactivatedUser)
    {
        $request->validate([
            'reactivation_reason' => 'required|string|max:500',
            'selected_account' => 'nullable|exists:deactivated_users,id',
        ]);

        try {
            // Si se selecciona una cuenta específica, usar esa
            if ($request->filled('selected_account')) {
                $selectedAccount = DeactivatedUser::findOrFail($request->selected_account);
                $deactivatedUser = $selectedAccount;
            }

            // Verificar si puede ser reactivado
            if (!$deactivatedUser->canBeReactivated()) {
                return back()->withErrors(['error' => 'Este usuario no puede ser reactivado automáticamente.']);
            }

            // Reactivar usando el servicio
            $success = $this->deactivationService->reactivateUser(
                $deactivatedUser, 
                $request->reactivation_reason
            );

            if ($success) {
                Log::info('Usuario reactivado por administrador', [
                    'admin_id' => auth()->id(),
                    'deactivated_user_id' => $deactivatedUser->id,
                    'email' => $deactivatedUser->email,
                    'reason' => $request->reactivation_reason,
                ]);

                return back()->with('success', 'Usuario reactivado exitosamente.');
            }

            return back()->withErrors(['error' => 'Error al reactivar el usuario.']);

        } catch (\Exception $e) {
            Log::error('Error al reactivar usuario por admin', [
                'admin_id' => auth()->id(),
                'deactivated_user_id' => $deactivatedUser->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Error inesperado: ' . $e->getMessage()]);
        }
    }

    /**
     * Suspender un usuario activo
     */
    public function suspendUser(Request $request, User $user)
    {
        $request->validate([
            'suspension_reason' => 'required|string|max:500',
            'suspension_notes' => 'nullable|string|max:1000',
        ]);

        try {
            $reason = $request->suspension_reason === 'other' ? 'policy_violation' : $request->suspension_reason;
            
            $success = $this->deactivationService->deactivateUser(
                $user,
                $reason,
                $request->suspension_notes,
                'admin_' . auth()->id()
            );

            if ($success) {
                Log::info('Usuario suspendido por administrador', [
                    'admin_id' => auth()->id(),
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'reason' => $reason,
                    'notes' => $request->suspension_notes,
                ]);

                return back()->with('success', 'Usuario suspendido exitosamente.');
            }

            return back()->withErrors(['error' => 'Error al suspender el usuario.']);

        } catch (\Exception $e) {
            Log::error('Error al suspender usuario', [
                'admin_id' => auth()->id(),
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Error inesperado: ' . $e->getMessage()]);
        }
    }

    /**
     * Ver historial completo de un usuario
     */
    public function userHistory(string $email)
    {
        // Buscar todas las instancias del usuario (activas y desactivadas)
        $activeUser = User::where('email', $email)->first();
        $deactivatedUsers = DeactivatedUser::where('email', $email)
            ->orderBy('deactivated_at', 'desc')
            ->get();

        // Combinar historial
        $history = collect();

        if ($activeUser) {
            $history->push([
                'type' => 'active',
                'user' => $activeUser,
                'status' => 'Activo',
                'date' => $activeUser->created_at,
                'data' => null,
            ]);
        }

        foreach ($deactivatedUsers as $deactivatedUser) {
            $history->push([
                'type' => 'deactivated',
                'user' => $deactivatedUser,
                'status' => 'Desactivado',
                'date' => $deactivatedUser->deactivated_at,
                'data' => $deactivatedUser->deactivation_data,
            ]);
        }

        $history = $history->sortByDesc('date');

        return view('admin.users.history', compact('history', 'email'));
    }

    /**
     * Exportar datos de usuarios desactivados
     */
    public function exportDeactivatedUsers(Request $request)
    {
        $query = DeactivatedUser::query();

        if ($request->filled('reason')) {
            $query->where('deactivation_reason', $request->reason);
        }

        $deactivatedUsers = $query->get();

        $filename = 'usuarios_desactivados_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($deactivatedUsers) {
            $file = fopen('php://output', 'w');
            
            // Headers del CSV
            fputcsv($file, [
                'ID', 'Nombre', 'Email', 'Motivo', 'Fecha Desactivación',
                'Notas', 'IP', 'User Agent', 'Reservas', 'Reseñas'
            ]);

            foreach ($deactivatedUsers as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->deactivation_reason_text,
                    $user->deactivated_at->format('Y-m-d H:i:s'),
                    $user->deactivation_notes,
                    $user->deactivation_ip,
                    $user->deactivation_user_agent,
                    $user->deactivation_data['reservations_count'] ?? 0,
                    $user->deactivation_data['reviews_count'] ?? 0,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Limpiar usuarios desactivados antiguos
     */
    public function cleanupDeactivatedUsers(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:30|max:3650',
            'reason' => 'nullable|string',
        ]);

        try {
            $days = $request->days;
            $reason = $request->reason;

            $deletedCount = $this->deactivationService->cleanupOldDeactivatedUsers($days);

            Log::info('Limpieza de usuarios desactivados por admin', [
                'admin_id' => auth()->id(),
                'days' => $days,
                'reason' => $reason,
                'deleted_count' => $deletedCount,
            ]);

            return back()->with('success', "Se limpiaron {$deletedCount} usuarios desactivados de más de {$days} días.");

        } catch (\Exception $e) {
            Log::error('Error en limpieza de usuarios desactivados', [
                'admin_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Error al limpiar usuarios: ' . $e->getMessage()]);
        }
    }

    public function reports()
    {
        return view('admin.reports');
    }

    public function exportReservations()
    {
        return view('admin.export');
    }

    public function settings()
    {
        return view('admin.settings');
    }
}
