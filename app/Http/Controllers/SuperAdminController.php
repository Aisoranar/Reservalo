<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\MembershipPlan;
use App\Models\Membership;
use App\Models\AuditLog;
use App\Models\SystemSetting;
use App\Models\Property;
use App\Models\Reservation;

class SuperAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:superadmin']);
    }

    /**
     * Dashboard principal
     */
    public function dashboard()
    {
        // Estadísticas generales
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'total_properties' => Property::count(),
            'total_reservations' => Reservation::count(),
            'active_memberships' => Membership::where('status', 'active')->where('expires_at', '>', now())->count(),
            'expiring_memberships' => Membership::where('status', 'active')
                ->where('expires_at', '<=', now()->addDays(7))
                ->where('expires_at', '>', now())
                ->count(),
            'total_roles' => Role::count(),
            'total_permissions' => Permission::count(),
        ];

        // Estadísticas de membresías por plan
        $membershipStats = Membership::select('membership_plan_id')
            ->selectRaw('COUNT(*) as count')
            ->with('plan:id,name')
            ->groupBy('membership_plan_id')
            ->get();

        // Actividad reciente
        $recentActivity = AuditLog::getRecent(7, 20);

        // Usuarios con membresías próximas a expirar
        $expiringMemberships = Membership::expiringSoon(7)
            ->with(['user:id,name,email', 'plan:id,name'])
            ->get();

        return view('superadmin.dashboard', compact(
            'stats',
            'membershipStats',
            'recentActivity',
            'expiringMemberships'
        ));
    }

    /**
     * Gestión de usuarios
     */
    public function users(Request $request)
    {
        $query = User::with(['roles', 'currentMembership.plan']);
        
        // Aplicar filtros
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } else {
                $query->where('is_active', false);
            }
        }
        
        if ($request->filled('account_type')) {
            $query->where('account_type', $request->account_type);
        }
        
        $users = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('superadmin.users', compact('users'));
    }

    /**
     * Gestión de roles
     */
    public function roles()
    {
        $roles = Role::with(['permissions', 'users'])->get();
        
        return view('superadmin.roles', compact('roles'));
    }

    /**
     * Gestión de permisos
     */
    public function permissions(Request $request)
    {
        $query = Permission::query();
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('display_name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } else {
                $query->where('is_active', false);
            }
        }
        
        $permissions = $query->orderBy('category')->orderBy('name')->get();
        $permissionsByCategory = $permissions->groupBy('category');
        $categories = Permission::getCategories();
        
        return view('superadmin.permissions', compact('permissions', 'permissionsByCategory', 'categories'));
    }

    /**
     * Gestión de planes de membresía
     */
    public function membershipPlans()
    {
        $plans = MembershipPlan::withCount('memberships')->get();
        
        return view('superadmin.membership-plans', compact('plans'));
    }

    /**
     * Gestión de membresías
     */
    public function memberships(Request $request)
    {
        $query = Membership::with(['user', 'plan']);
        
        if ($request->filled('search')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('plan')) {
            $query->where('membership_plan_id', $request->plan);
        }
        
        if ($request->filled('expiration')) {
            if ($request->expiration === 'expiring_soon') {
                $query->where('status', 'active')
                      ->where('expires_at', '<=', now()->addDays(7))
                      ->where('expires_at', '>', now());
            } elseif ($request->expiration === 'expired') {
                $query->where('expires_at', '<=', now());
            }
        }
        
        $memberships = $query->orderBy('created_at', 'desc')->paginate(15);
        $plans = MembershipPlan::all();
        
        return view('superadmin.memberships', compact('memberships', 'plans'));
    }

    /**
     * Configuraciones del sistema
     */
    public function settings()
    {
        $settings = SystemSetting::getSystemSettings();
        
        return view('superadmin.settings', compact('settings'));
    }

    /**
     * Actualizar configuraciones
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:20',
            'site_address' => 'nullable|string|max:500',
            'site_description' => 'nullable|string|max:1000',
        ]);

        $settings = $request->only([
            'site_name', 'contact_email', 'contact_phone', 'site_address', 'site_description',
            'site_active', 'maintenance_mode', 'registration_enabled', 'email_verification_required',
            'maintenance_message', 'membership_notification_days', 'membership_grace_period',
            'max_trial_periods', 'auto_renewal_enabled', 'membership_required',
            'email_notifications_enabled', 'push_notifications_enabled', 'admin_notifications_enabled',
            'user_notifications_enabled'
        ]);

        $changes = [];
        $oldValues = [];
        $newValues = [];

        foreach ($settings as $key => $value) {
            $oldValue = SystemSetting::get($key);
            
            // Convertir valores booleanos
            if (in_array($key, ['site_active', 'maintenance_mode', 'registration_enabled', 'email_verification_required', 
                                'auto_renewal_enabled', 'membership_required', 'email_notifications_enabled', 
                                'push_notifications_enabled', 'admin_notifications_enabled', 'user_notifications_enabled'])) {
                $value = (bool) $value;
            }
            
            // Solo registrar si el valor cambió
            if ($oldValue != $value) {
                $changes[$key] = [
                    'old' => $oldValue,
                    'new' => $value
                ];
                $oldValues[$key] = $oldValue;
                $newValues[$key] = $value;
                
                // Actualizar el valor
                if (is_bool($value)) {
                    SystemSetting::set($key, $value, 'boolean');
                } else {
                    SystemSetting::set($key, $value);
                }
            }
        }

        // Registrar en auditoría si hubo cambios
        if (!empty($changes)) {
            AuditLog::log(
                auth()->user()->id,
                'settings_updated',
                'SystemSetting',
                null,
                $oldValues,
                $newValues,
                $request->ip(),
                $request->userAgent(),
                'Configuraciones del sistema actualizadas: ' . implode(', ', array_keys($changes))
            );
        }

        $message = !empty($changes) 
            ? 'Configuraciones actualizadas correctamente (' . count($changes) . ' cambios)'
            : 'No se detectaron cambios en las configuraciones';

        return redirect()->route('superadmin.settings')
            ->with('success', $message);
    }

    /**
     * Alternar estado del sistema
     */
    public function toggleSystemStatus(Request $request)
    {
        $currentStatus = SystemSetting::get('site_active', false);
        $newStatus = !$currentStatus;
        
        SystemSetting::set('site_active', $newStatus, 'boolean');
        
        // Registrar en auditoría
        AuditLog::log(
            auth()->user()->id,
            'system_toggled',
            'SystemSetting',
            null,
            ['site_active' => $currentStatus],
            ['site_active' => $newStatus],
            $request->ip(),
            $request->userAgent(),
            'Estado del sistema ' . ($newStatus ? 'activado' : 'desactivado')
        );
        
        $status = $newStatus ? 'activado' : 'desactivado';
        
        return redirect()->back()
            ->with('success', "Sistema {$status} correctamente");
    }

    /**
     * Alternar modo mantenimiento
     */
    public function toggleMaintenanceMode(Request $request)
    {
        $currentMode = SystemSetting::get('maintenance_mode', false);
        $newMode = !$currentMode;
        
        SystemSetting::set('maintenance_mode', $newMode, 'boolean');
        
        // Registrar en auditoría
        AuditLog::log(
            auth()->user()->id,
            'maintenance_toggled',
            'SystemSetting',
            null,
            ['maintenance_mode' => $currentMode],
            ['maintenance_mode' => $newMode],
            $request->ip(),
            $request->userAgent(),
            'Modo mantenimiento ' . ($newMode ? 'activado' : 'desactivado')
        );
        
        $mode = $newMode ? 'activado' : 'desactivado';
        
        return redirect()->back()
            ->with('success', "Modo mantenimiento {$mode} correctamente");
    }

    /**
     * Restaurar configuraciones a valores por defecto
     */
    public function resetSettings(Request $request)
    {
        try {
            // Obtener configuraciones actuales antes de restaurar
            $currentSettings = SystemSetting::getSystemSettings();
            
            // Restaurar configuraciones por defecto
            SystemSetting::initializeDefaults();
            
            // Registrar en auditoría
            AuditLog::log(
                auth()->user()->id,
                'settings_reset',
                'SystemSetting',
                null,
                $currentSettings,
                SystemSetting::getSystemSettings(),
                $request->ip(),
                $request->userAgent(),
                'Configuraciones del sistema restauradas a valores por defecto'
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Configuraciones restauradas a valores por defecto correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al restaurar las configuraciones: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reportes
     */
    public function reports(Request $request)
    {
        $type = $request->get('type', 'general');
        $period = $request->get('period', 30);
        
        // Estadísticas básicas
        $stats = [
            'total_users' => User::count(),
            'active_memberships' => Membership::where('status', 'active')->count(),
            'total_properties' => Property::count(),
            'total_reservations' => Reservation::count(),
        ];
        
        // Datos para gráficos
        $chartData = [
            'users' => $this->getUsersChartData($period),
            'memberships' => $this->getMembershipsChartData(),
        ];
        
        // Actividad reciente
        $recentActivity = AuditLog::getRecent(7, 20);
        
        return view('superadmin.reports', compact('stats', 'chartData', 'recentActivity'));
    }

    /**
     * Logs de auditoría
     */
    public function auditLogs(Request $request)
    {
        $query = AuditLog::with('user');
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('description', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($userQuery) use ($request) {
                      $userQuery->where('name', 'like', '%' . $request->search . '%')
                               ->orWhere('email', 'like', '%' . $request->search . '%');
                  });
            });
        }
        
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        
        if ($request->filled('model')) {
            $query->where('model_type', $request->model);
        }
        
        if ($request->filled('user')) {
            $query->where('user_id', $request->user);
        }
        
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }
        
        $logs = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Estadísticas
        $stats = [
            'total_logs' => AuditLog::count(),
            'today_logs' => AuditLog::whereDate('created_at', today())->count(),
            'week_logs' => AuditLog::where('created_at', '>=', now()->subWeek())->count(),
            'active_users' => User::where('is_active', true)->count(),
        ];
        
        $users = User::select('id', 'name')->get();
        
        return view('superadmin.audit-logs', compact('logs', 'stats', 'users'));
    }

    /**
     * Exportar reportes
     */
    public function exportReports(Request $request)
    {
        $type = $request->get('type', 'general');
        $period = $request->get('period', 30);
        
        // Aquí implementarías la lógica de exportación
        // Por ahora, devolvemos un JSON con los datos
        $data = [
            'type' => $type,
            'period' => $period,
            'exported_at' => now(),
            'message' => 'Exportación de reportes implementada'
        ];
        
        return response()->json($data);
    }

    /**
     * Exportar logs de auditoría
     */
    public function exportAuditLogs(Request $request)
    {
        $query = AuditLog::with('user');
        
        // Aplicar los mismos filtros que en auditLogs
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('description', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($userQuery) use ($request) {
                      $userQuery->where('name', 'like', '%' . $request->search . '%')
                               ->orWhere('email', 'like', '%' . $request->search . '%');
                  });
            });
        }
        
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        
        if ($request->filled('model')) {
            $query->where('model_type', $request->model);
        }
        
        if ($request->filled('user')) {
            $query->where('user_id', $request->user);
        }
        
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }
        
        $logs = $query->orderBy('created_at', 'desc')->get();
        
        // Aquí implementarías la exportación real (CSV, Excel, etc.)
        // Por ahora, devolvemos un JSON
        return response()->json([
            'success' => true,
            'count' => $logs->count(),
            'exported_at' => now(),
            'message' => 'Logs de auditoría exportados correctamente'
        ]);
    }

    /**
     * Limpiar logs de auditoría antiguos
     */
    public function cleanupAuditLogs(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:30|max:365'
        ]);
        
        $days = $request->days;
        $cutoffDate = now()->subDays($days);
        
        $deletedCount = AuditLog::where('created_at', '<', $cutoffDate)->delete();
        
        return response()->json([
            'success' => true,
            'deleted_count' => $deletedCount,
            'message' => "Se eliminaron {$deletedCount} logs más antiguos de {$days} días"
        ]);
    }

    /**
     * Obtener datos para gráfico de usuarios
     */
    private function getUsersChartData($period)
    {
        $labels = [];
        $data = [];
        
        for ($i = $period - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('M d');
            $data[] = User::whereDate('created_at', $date)->count();
        }
        
        return compact('labels', 'data');
    }

    /**
     * Obtener datos para gráfico de membresías
     */
    private function getMembershipsChartData()
    {
        $memberships = Membership::select('membership_plan_id')
            ->selectRaw('COUNT(*) as count')
            ->with('plan:id,name')
            ->groupBy('membership_plan_id')
            ->get();
        
        $labels = $memberships->pluck('plan.name')->toArray();
        $data = $memberships->pluck('count')->toArray();
        
        return compact('labels', 'data');
    }
}