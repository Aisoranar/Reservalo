<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
        $this->middleware(['auth', 'roles:superadmin,admin']);
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
        
        $perPage = $request->get('per_page', 15);
        $users = $query->orderBy('created_at', 'desc')->paginate($perPage);
        
        return view('superadmin.users', compact('users'));
    }

    /**
     * Mostrar formulario para crear usuario
     */
    public function createUser()
    {
        $roles = Role::all();
        $departments = \App\Models\Department::active()->get();
        
        return view('superadmin.users.create', compact('roles', 'departments'));
    }

    /**
     * Almacenar nuevo usuario
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:user,admin,superadmin',
            'account_type' => 'required|string|in:individual,business',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|string|in:male,female,other',
            'bio' => 'nullable|string|max:1000',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'whatsapp' => $request->whatsapp,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'account_type' => $request->account_type,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'postal_code' => $request->postal_code,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'bio' => $request->bio,
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        // Asignar rol
        $role = Role::where('name', $request->role)->first();
        if ($role) {
            $user->roles()->attach($role->id, [
                'assigned_at' => now(),
                'assigned_by' => auth()->id()
            ]);
        }

        // Registrar en auditoría
        AuditLog::log(
            'user_created',
            'User',
            $user->id,
            auth()->id(),
            [],
            $user->toArray(),
            'Usuario creado: ' . $user->name
        );

        return redirect()->route('superadmin.users')
            ->with('success', 'Usuario creado correctamente');
    }

    /**
     * Mostrar usuario específico
     */
    public function showUser(User $user)
    {
        $user->load(['roles', 'memberships.plan', 'ownedProperties', 'auditLogs' => function($query) {
            $query->latest()->limit(10);
        }]);
        
        return view('superadmin.users.show', compact('user'));
    }

    /**
     * Mostrar formulario para editar usuario
     */
    public function editUser(User $user)
    {
        $roles = Role::all();
        $departments = \App\Models\Department::active()->get();
        $user->load('roles');
        
        return view('superadmin.users.edit', compact('user', 'roles', 'departments'));
    }

    /**
     * Actualizar usuario
     */
    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|string|in:user,admin,superadmin',
            'account_type' => 'required|string|in:individual,business',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|string|in:male,female,other',
            'bio' => 'nullable|string|max:1000',
        ]);

        $oldData = $user->toArray();
        
        $updateData = $request->except(['password', 'password_confirmation', 'role']);
        
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        // Actualizar rol si cambió
        if ($request->role !== $user->role) {
            $user->roles()->detach();
            $role = Role::where('name', $request->role)->first();
            if ($role) {
                $user->roles()->attach($role->id, [
                    'assigned_at' => now(),
                    'assigned_by' => auth()->id()
                ]);
            }
            $user->update(['role' => $request->role]);
        }

        // Registrar en auditoría
        AuditLog::log(
            'user_updated',
            'User',
            $user->id,
            auth()->id(),
            $oldData,
            $user->toArray(),
            'Usuario actualizado: ' . $user->name
        );

        return redirect()->route('superadmin.users')
            ->with('success', 'Usuario actualizado correctamente');
    }

    /**
     * Eliminar usuario
     */
    public function destroyUser(User $user)
    {
        // Validaciones de seguridad
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'No puedes eliminar tu propia cuenta');
        }

        // Verificar si el usuario tiene reservas activas
        $activeReservations = $user->reservations()
            ->whereIn('status', ['pending', 'approved', 'confirmed'])
            ->count();

        if ($activeReservations > 0) {
            return redirect()->back()
                ->with('error', "No se puede eliminar el usuario porque tiene {$activeReservations} reserva(s) activa(s). Primero debe cancelar o completar las reservas.");
        }

        // Verificar si es superadmin y hay otros superadmins
        if ($user->role === 'superadmin') {
            $superAdminCount = User::where('role', 'superadmin')->count();
            if ($superAdminCount <= 1) {
                return redirect()->back()
                    ->with('error', 'No se puede eliminar el último superadministrador del sistema');
            }
        }

        try {
            $oldData = $user->toArray();
            $userName = $user->name;
            $userEmail = $user->email;
            
            // Eliminar relaciones primero (si es necesario)
            // Las reservas se mantienen por integridad referencial
            // pero se pueden marcar como "usuario eliminado"
            
            $user->delete();

            // Registrar en auditoría
            AuditLog::log(
                'user_deleted',
                'User',
                $user->id,
                auth()->id(),
                $oldData,
                [],
                "Usuario eliminado: {$userName} ({$userEmail})"
            );

            return redirect()->route('superadmin.users')
                ->with('success', "Usuario '{$userName}' eliminado correctamente");

        } catch (\Exception $e) {
            \Log::error('Error eliminando usuario: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Error al eliminar el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Alternar estado del usuario
     */
    public function toggleUserStatus(Request $request, User $user)
    {
        // Check if this is a JSON request
        $isJsonRequest = $request->expectsJson() || 
                        $request->wantsJson() || 
                        $request->ajax() || 
                        $request->isJson() ||
                        $request->header('Accept') === 'application/json' ||
                        $request->header('Content-Type') === 'application/json';
        
        try {
            if ($user->id === auth()->id()) {
            if ($isJsonRequest) {
                return response()->json(['success' => false, 'message' => 'No puedes cambiar el estado de tu propia cuenta'], 403);
            }
                return redirect()->back()
                    ->with('error', 'No puedes cambiar el estado de tu propia cuenta');
            }

            $oldStatus = $user->is_active;
            
            // Si se envía un estado específico en el request, usarlo; si no, alternar
            if ($request->has('is_active')) {
                $newStatus = (bool) $request->is_active;
            } else {
                $newStatus = !$oldStatus;
            }
            
            // Actualizar solo el campo is_active para evitar problemas con account_type
            $user->is_active = $newStatus;
            $user->save();

            // Registrar en auditoría (temporalmente deshabilitado para evitar errores)
            try {
                AuditLog::log(
                    'user_status_toggled',
                    'User',
                    $user->id,
                    auth()->id(),
                    ['is_active' => $oldStatus],
                    ['is_active' => $newStatus],
                    'Estado del usuario ' . ($newStatus ? 'activado' : 'desactivado') . ': ' . $user->name
                );
            } catch (\Exception $auditError) {
                \Log::warning('Error en auditoría (ignorado): ' . $auditError->getMessage());
            }

            $status = $newStatus ? 'activado' : 'desactivado';
            
            if ($isJsonRequest) {
                return response()->json([
                    'success' => true, 
                    'message' => "Usuario {$status} correctamente",
                    'is_active' => $newStatus
                ]);
            }
            
            return redirect()->back()
                ->with('success', "Usuario {$status} correctamente");
                
        } catch (\Exception $e) {
            \Log::error('Error en toggleUserStatus: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($isJsonRequest) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Error interno del servidor: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Error interno del servidor');
        }
    }

    /**
     * Asignar rol a usuario
     */
    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id'
        ]);

        $role = Role::findOrFail($request->role_id);
        
        if (!$user->roles()->where('role_id', $role->id)->exists()) {
            $user->roles()->attach($role->id, [
                'assigned_at' => now(),
                'assigned_by' => auth()->id()
            ]);

            // Registrar en auditoría
            AuditLog::log(
                'role_assigned',
                'User',
                $user->id,
                auth()->id(),
                [],
                ['role_id' => $role->id, 'role_name' => $role->name],
                'Rol asignado: ' . $role->name . ' a ' . $user->name
            );

            return redirect()->back()
                ->with('success', 'Rol asignado correctamente');
        }

        return redirect()->back()
            ->with('warning', 'El usuario ya tiene este rol asignado');
    }

    /**
     * Remover rol de usuario
     */
    public function removeRole(User $user, Role $role)
    {
        $user->roles()->detach($role->id);

        // Registrar en auditoría
        AuditLog::log(
            'role_removed',
            'User',
            $user->id,
            auth()->id(),
            ['role_id' => $role->id, 'role_name' => $role->name],
            [],
            'Rol removido: ' . $role->name . ' de ' . $user->name
        );

        return redirect()->back()
            ->with('success', 'Rol removido correctamente');
    }

    /**
     * Gestión de roles
     */
    public function roles()
    {
        $roles = Role::with(['rolePermissions', 'users'])->get();
        
        return view('superadmin.roles', compact('roles'));
    }

    /**
     * Mostrar formulario para crear rol
     */
    public function createRole()
    {
        $permissions = Permission::all();
        
        return view('superadmin.roles.create', compact('permissions'));
    }

    /**
     * Almacenar nuevo rol
     */
    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description,
            'is_active' => true,
        ]);

        // Asignar permisos
        if ($request->has('permissions')) {
            $role->rolePermissions()->attach($request->permissions);
        }

        // Registrar en auditoría
        AuditLog::log(
            'role_created',
            'Role',
            $role->id,
            auth()->id(),
            [],
            $role->toArray(),
            'Rol creado: ' . $role->display_name
        );

        return redirect()->route('superadmin.roles')
            ->with('success', 'Rol creado correctamente');
    }

    /**
     * Mostrar rol específico
     */
    public function showRole(Role $role)
    {
        $role->load(['rolePermissions', 'users']);
        
        return view('superadmin.roles.show', compact('role'));
    }

    /**
     * Mostrar formulario para editar rol
     */
    public function editRole(Role $role)
    {
        $permissions = Permission::all();
        $role->load('rolePermissions');
        
        return view('superadmin.roles.edit', compact('role', 'permissions'));
    }

    /**
     * Actualizar rol
     */
    public function updateRole(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $oldData = $role->toArray();
        
        $role->update([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description,
        ]);

        // Actualizar permisos
        if ($request->has('permissions')) {
            $role->rolePermissions()->sync($request->permissions);
        } else {
            $role->rolePermissions()->detach();
        }

        // Registrar en auditoría
        AuditLog::log(
            'role_updated',
            'Role',
            $role->id,
            auth()->id(),
            $oldData,
            $role->toArray(),
            'Rol actualizado: ' . $role->display_name
        );

        return redirect()->route('superadmin.roles')
            ->with('success', 'Rol actualizado correctamente');
    }

    /**
     * Eliminar rol
     */
    public function destroyRole(Role $role)
    {
        $oldData = $role->toArray();
        $roleName = $role->display_name;
        
        // Verificar si el rol tiene usuarios asignados
        if ($role->users()->count() > 0) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar el rol porque tiene usuarios asignados');
        }
        
        $role->delete();

        // Registrar en auditoría
        AuditLog::log(
            'role_deleted',
            'Role',
            $role->id,
            auth()->id(),
            $oldData,
            [],
            'Rol eliminado: ' . $roleName
        );

        return redirect()->route('superadmin.roles')
            ->with('success', 'Rol eliminado correctamente');
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
        // Forzar recarga de datos sin caché
        $plans = MembershipPlan::withCount('memberships')
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Forzar recarga de cada plan individualmente
        $plans->each(function($plan) {
            $plan->refresh();
        });
        
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
                'settings_updated',
                'SystemSetting',
                null,
                auth()->user()->id,
                $oldValues,
                $newValues,
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
            'system_toggled',
            'SystemSetting',
            null,
            auth()->user()->id,
            ['site_active' => $currentStatus],
            ['site_active' => $newStatus],
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
            'maintenance_toggled',
            'SystemSetting',
            null,
            auth()->user()->id,
            ['maintenance_mode' => $currentMode],
            ['maintenance_mode' => $newMode],
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
            // Inicializar configuraciones por defecto
            SystemSetting::initializeDefaults();
            
            // Limpiar cache
            SystemSetting::clearCache();
            
            // Registrar en auditoría
            AuditLog::log(
                'settings_reset',
                'SystemSetting',
                null,
                auth()->user()->id,
                [],
                [],
                'Configuraciones del sistema restauradas a valores por defecto'
            );
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Configuraciones restauradas correctamente'
                ]);
            }
            
            return redirect()->route('superadmin.settings')
                ->with('success', 'Configuraciones restauradas a valores por defecto correctamente');
                
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al restaurar configuraciones: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('superadmin.settings')
                ->with('error', 'Error al restaurar configuraciones: ' . $e->getMessage());
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
     * Mostrar detalles de un log de auditoría
     */
    public function showAuditLog(AuditLog $log)
    {
        $log->load('user');
        
        return response()->json([
            'success' => true,
            'log' => [
                'id' => $log->id,
                'action' => $log->action,
                'model_type' => $log->model_type,
                'model_id' => $log->model_id,
                'description' => $log->description,
                'ip_address' => $log->ip_address,
                'user_agent' => $log->user_agent,
                'created_at' => $log->created_at->format('d/m/Y H:i:s'),
                'user' => $log->user ? [
                    'id' => $log->user->id,
                    'name' => $log->user->name,
                    'email' => $log->user->email
                ] : null,
                'old_values' => $log->old_values,
                'new_values' => $log->new_values
            ]
        ]);
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


    /**
     * Mostrar formulario para crear permiso
     */
    public function createPermission()
    {
        $categories = Permission::distinct()->pluck('category')->toArray();
        return view('superadmin.permissions.create', compact('categories'));
    }

    /**
     * Guardar nuevo permiso
     */
    public function storePermission(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'category' => 'required|string|max:100',
            'is_active' => 'boolean'
        ]);

        $permission = Permission::create([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description,
            'category' => $request->category,
            'is_active' => $request->boolean('is_active', true)
        ]);

        // Registrar en auditoría
        AuditLog::log(
            'permission_created',
            'Permission',
            $permission->id,
            auth()->id(),
            $permission->toArray(),
            [],
            'Permiso creado: ' . $permission->display_name
        );

        return redirect()->route('superadmin.permissions')
            ->with('success', 'Permiso creado correctamente');
    }

    /**
     * Mostrar detalles de permiso
     */
    public function showPermission(Permission $permission)
    {
        return view('superadmin.permissions.show', compact('permission'));
    }

    /**
     * Mostrar formulario para editar permiso
     */
    public function editPermission(Permission $permission)
    {
        $categories = Permission::distinct()->pluck('category')->toArray();
        return view('superadmin.permissions.edit', compact('permission', 'categories'));
    }

    /**
     * Actualizar permiso
     */
    public function updatePermission(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'category' => 'required|string|max:100',
            'is_active' => 'boolean'
        ]);

        $oldData = $permission->toArray();

        $permission->update([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description,
            'category' => $request->category,
            'is_active' => $request->boolean('is_active', true)
        ]);

        // Registrar en auditoría
        AuditLog::log(
            'permission_updated',
            'Permission',
            $permission->id,
            auth()->id(),
            $permission->toArray(),
            $oldData,
            'Permiso actualizado: ' . $permission->display_name
        );

        return redirect()->route('superadmin.permissions')
            ->with('success', 'Permiso actualizado correctamente');
    }

    /**
     * Eliminar permiso
     */
    public function destroyPermission(Permission $permission)
    {
        // Verificar si el permiso está siendo usado por algún rol
        $rolesUsingPermission = $permission->roles()->count();
        
        if ($rolesUsingPermission > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el permiso porque está siendo usado por ' . $rolesUsingPermission . ' rol(es)'
            ], 422);
        }

        $permissionName = $permission->display_name;
        $permission->delete();

        // Registrar en auditoría
        AuditLog::log(
            'permission_deleted',
            'Permission',
            $permission->id,
            auth()->id(),
            [],
            $permission->toArray(),
            'Permiso eliminado: ' . $permissionName
        );

        return response()->json([
            'success' => true,
            'message' => 'Permiso eliminado correctamente'
        ]);
    }

    /**
     * Cambiar estado de permiso
     */
    public function togglePermissionStatus(Request $request, Permission $permission)
    {
        $request->validate([
            'is_active' => 'required|boolean'
        ]);

        $oldStatus = $permission->is_active;
        $permission->is_active = $request->boolean('is_active');
        $permission->save();

        // Registrar en auditoría
        AuditLog::log(
            'permission_status_changed',
            'Permission',
            $permission->id,
            auth()->id(),
            ['is_active' => $permission->is_active],
            ['is_active' => $oldStatus],
            'Estado de permiso cambiado: ' . $permission->display_name . ' - ' . ($permission->is_active ? 'Activado' : 'Desactivado')
        );

        return response()->json([
            'success' => true,
            'message' => 'Estado del permiso actualizado correctamente',
            'is_active' => $permission->is_active
        ]);
    }

    /**
     * Mostrar formulario para crear plan de membresía
     */
    public function createMembershipPlan()
    {
        return view('superadmin.membership-plans.create');
    }

    /**
     * Guardar nuevo plan de membresía
     */
    public function storeMembershipPlan(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:membership_plans,name',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'max_properties' => 'nullable|integer|min:0',
            'max_reservations' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
            'is_default' => 'boolean'
        ]);

        // Si se marca como predeterminado, desmarcar otros
        if ($request->boolean('is_default')) {
            MembershipPlan::where('is_default', true)->update(['is_default' => false]);
        }

        $plan = MembershipPlan::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'duration_days' => $request->duration_days,
            'max_properties' => $request->max_properties,
            'max_reservations' => $request->max_reservations,
            'features' => $request->features ?? [],
            'is_active' => $request->boolean('is_active', true),
            'is_default' => $request->boolean('is_default', false)
        ]);

        // Registrar en auditoría
        AuditLog::log(
            'membership_plan_created',
            'MembershipPlan',
            $plan->id,
            auth()->id(),
            $plan->toArray(),
            [],
            'Plan de membresía creado: ' . $plan->name
        );

        return redirect()->route('superadmin.membership-plans')
            ->with('success', 'Plan de membresía creado correctamente');
    }

    /**
     * Mostrar detalles de plan de membresía
     */
    public function showMembershipPlan(MembershipPlan $plan)
    {
        $plan->loadCount('memberships');
        return view('superadmin.membership-plans.show', compact('plan'));
    }

    /**
     * Mostrar formulario para editar plan de membresía
     */
    public function editMembershipPlan(MembershipPlan $plan)
    {
        return view('superadmin.membership-plans.edit', compact('plan'));
    }

    /**
     * Actualizar plan de membresía
     */
    public function updateMembershipPlan(Request $request, MembershipPlan $plan)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:membership_plans,name,' . $plan->id,
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'max_properties' => 'nullable|integer|min:0',
            'max_reservations' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
            'is_default' => 'boolean'
        ]);

        $oldData = $plan->toArray();

        // Si se marca como predeterminado, desmarcar otros
        if ($request->boolean('is_default') && !$plan->is_default) {
            MembershipPlan::where('is_default', true)->update(['is_default' => false]);
        }

        $plan->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'duration_days' => $request->duration_days,
            'max_properties' => $request->max_properties,
            'max_reservations' => $request->max_reservations,
            'features' => $request->features ?? [],
            'is_active' => $request->boolean('is_active', true),
            'is_default' => $request->boolean('is_default', false)
        ]);

        // Registrar en auditoría
        AuditLog::log(
            'membership_plan_updated',
            'MembershipPlan',
            $plan->id,
            auth()->id(),
            $plan->toArray(),
            $oldData,
            'Plan de membresía actualizado: ' . $plan->name
        );

        return redirect()->route('superadmin.membership-plans')
            ->with('success', 'Plan de membresía actualizado correctamente');
    }

    /**
     * Eliminar plan de membresía
     */
    public function destroyMembershipPlan(MembershipPlan $plan)
    {
        // Verificar si el plan tiene membresías activas
        $activeMemberships = $plan->memberships()->where('expires_at', '>', now())->count();
        
        if ($activeMemberships > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el plan porque tiene ' . $activeMemberships . ' membresía(s) activa(s)'
            ], 422);
        }

        $planName = $plan->name;
        $plan->delete();

        // Registrar en auditoría
        AuditLog::log(
            'membership_plan_deleted',
            'MembershipPlan',
            $plan->id,
            auth()->id(),
            [],
            $plan->toArray(),
            'Plan de membresía eliminado: ' . $planName
        );

        return response()->json([
            'success' => true,
            'message' => 'Plan de membresía eliminado correctamente'
        ]);
    }

    /**
     * Cambiar estado de plan de membresía
     */
    public function toggleMembershipPlanStatus(Request $request, MembershipPlan $plan)
    {
        $request->validate([
            'is_active' => 'required|boolean'
        ]);

        $oldStatus = $plan->is_active;
        $plan->is_active = $request->boolean('is_active');
        $plan->save();

        // Registrar en auditoría
        AuditLog::log(
            'membership_plan_status_changed',
            'MembershipPlan',
            $plan->id,
            auth()->id(),
            ['is_active' => $plan->is_active],
            ['is_active' => $oldStatus],
            'Estado de plan cambiado: ' . $plan->name . ' - ' . ($plan->is_active ? 'Activado' : 'Desactivado')
        );

        return response()->json([
            'success' => true,
            'message' => 'Estado del plan actualizado correctamente',
            'is_active' => $plan->is_active
        ]);
    }

    /**
     * Establecer plan como predeterminado
     */
    public function setDefaultMembershipPlan(MembershipPlan $plan)
    {
        // Desmarcar otros planes como predeterminados
        MembershipPlan::where('is_default', true)->update(['is_default' => false]);
        
        // Marcar este plan como predeterminado
        $plan->is_default = true;
        $plan->save();

        // Registrar en auditoría
        AuditLog::log(
            'membership_plan_set_default',
            'MembershipPlan',
            $plan->id,
            auth()->id(),
            ['is_default' => true],
            ['is_default' => false],
            'Plan establecido como predeterminado: ' . $plan->name
        );

        return response()->json([
            'success' => true,
            'message' => 'Plan establecido como predeterminado correctamente'
        ]);
    }

    /**
     * Gestión de reservas
     */
    public function reservations(Request $request)
    {
        $query = \App\Models\Reservation::with(['user', 'property']);

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->whereHas('user', function($userQuery) use ($request) {
                    $userQuery->where('name', 'like', '%' . $request->search . '%')
                             ->orWhere('email', 'like', '%' . $request->search . '%');
                })
                ->orWhereHas('property', function($propertyQuery) use ($request) {
                    $propertyQuery->where('title', 'like', '%' . $request->search . '%');
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('end_date', '<=', $request->date_to);
        }

        // Obtener elementos por página del request
        $perPage = $request->get('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50, 100]) ? $perPage : 15;

        // Ordenar por prioridad: pendientes primero (más viejas primero), luego por fecha de creación
        $reservations = $query->orderByRaw("
            CASE 
                WHEN status = 'pending' THEN 0 
                WHEN status = 'approved' THEN 1 
                WHEN status = 'rejected' THEN 2 
                WHEN status = 'cancelled' THEN 3 
                WHEN status = 'deleted' THEN 4 
                ELSE 5 
            END
        ")->orderBy('created_at', 'asc')->paginate($perPage);
        
        return view('superadmin.reservations.index', compact('reservations'));
    }

    /**
     * Reservas pendientes de aprobación
     */
    public function pendingReservations()
    {
        $reservations = \App\Models\Reservation::with(['user', 'property'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('superadmin.reservations.pending', compact('reservations'));
    }

    /**
     * Mostrar detalles de reserva
     */
    public function showReservation(\App\Models\Reservation $reservation)
    {
        $reservation->load(['user', 'property']);
        return view('superadmin.reservations.show', compact('reservation'));
    }

    /**
     * Aprobar reserva
     */
    public function approveReservation(Request $request, \App\Models\Reservation $reservation)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500'
        ]);

        $reservation->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'admin_notes' => $request->notes
        ]);

        // Registrar en auditoría
        AuditLog::log(
            'reservation_approved',
            'Reservation',
            $reservation->id,
            auth()->id(),
            ['status' => 'approved'],
            ['status' => 'pending'],
            'Reserva aprobada: ' . $reservation->id
        );

        // Notificar al usuario
        \App\Models\Notification::createSystemNotification(
            $reservation->user_id,
            'Reserva Aprobada',
            "Tu reserva #{$reservation->id} ha sido aprobada y confirmada.",
            ['reservation_id' => $reservation->id],
            false,
            auth()->id()
        );

        // Enviar correo de notificación
        try {
            $emailData = [
                'user_name' => $reservation->user->name,
                'property_title' => $reservation->property->name,
                'property_location' => $reservation->property->location,
                'check_in_date' => $reservation->check_in->format('d/m/Y'),
                'check_in_time' => $reservation->check_in->format('H:i'),
                'check_out_date' => $reservation->check_out->format('d/m/Y'),
                'check_out_time' => $reservation->check_out->format('H:i'),
                'guests' => $reservation->guests,
                'nights' => $reservation->nights,
                'total_amount' => number_format($reservation->total_amount, 0, ',', '.'),
                'admin_notes' => $request->notes ?? ''
            ];

            \Mail::to($reservation->user->email)->send(
                new \App\Mail\ReservationNotification('reservation_approved', $emailData)
            );
        } catch (\Exception $e) {
            \Log::error('Error enviando correo de aprobación: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Reserva aprobada correctamente'
        ]);
    }

    /**
     * Rechazar reserva
     */
    public function rejectReservation(Request $request, \App\Models\Reservation $reservation)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $reservation->update([
            'status' => 'rejected',
            'rejected_by' => auth()->id(),
            'rejected_at' => now(),
            'rejection_reason' => $request->reason
        ]);

        // Registrar en auditoría
        AuditLog::log(
            'reservation_rejected',
            'Reservation',
            $reservation->id,
            auth()->id(),
            ['status' => 'rejected'],
            ['status' => 'pending'],
            'Reserva rechazada: ' . $reservation->id
        );

        // Notificar al usuario
        \App\Models\Notification::createSystemNotification(
            $reservation->user_id,
            'Reserva Rechazada',
            "Tu reserva #{$reservation->id} ha sido rechazada. Motivo: {$request->reason}",
            ['reservation_id' => $reservation->id, 'reason' => $request->reason],
            false,
            auth()->id()
        );

        // Enviar correo de notificación
        try {
            $emailData = [
                'user_name' => $reservation->user->name,
                'property_title' => $reservation->property->name,
                'property_location' => $reservation->property->location,
                'check_in_date' => $reservation->check_in->format('d/m/Y'),
                'check_out_date' => $reservation->check_out->format('d/m/Y'),
                'guests' => $reservation->guests,
                'rejection_reason' => $request->reason
            ];

            \Mail::to($reservation->user->email)->send(
                new \App\Mail\ReservationNotification('reservation_rejected', $emailData)
            );
        } catch (\Exception $e) {
            \Log::error('Error enviando correo de rechazo: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Reserva rechazada correctamente'
        ]);
    }

    /**
     * Enviar correo manual a usuario de reserva
     */
    public function sendEmail(Request $request, \App\Models\Reservation $reservation)
    {
        $request->validate([
            'email_type' => 'required|string|in:reservation_approved,reservation_rejected,reservation_reminder,reservation_cancelled',
            'subject' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:1000'
        ]);

        try {
            // Preparar datos del correo
            $emailData = [
                'user_name' => $reservation->user->name,
                'property_title' => $reservation->property->name,
                'property_location' => $reservation->property->location,
                'check_in_date' => $reservation->check_in ? $reservation->check_in->format('d/m/Y') : 'N/A',
                'check_in_time' => $reservation->check_in ? $reservation->check_in->format('H:i') : 'N/A',
                'check_out_date' => $reservation->check_out ? $reservation->check_out->format('d/m/Y') : 'N/A',
                'check_out_time' => $reservation->check_out ? $reservation->check_out->format('H:i') : 'N/A',
                'guests' => $reservation->guests ?? 'N/A',
                'nights' => $reservation->nights ?? 0,
                'total_amount' => $reservation->total_amount ? number_format($reservation->total_amount, 0, ',', '.') : 'N/A',
                'admin_notes' => $request->message ?? '',
                'custom_subject' => $request->subject ?? null
            ];

            // Enviar correo
            \Mail::to($reservation->user->email)->send(
                new \App\Mail\ReservationNotification($request->email_type, $emailData)
            );

            // Registrar en auditoría
            AuditLog::log(
                'email_sent',
                'Reservation',
                $reservation->id,
                auth()->id(),
                ['email_type' => $request->email_type, 'user_email' => $reservation->user->email],
                [],
                'Correo enviado manualmente: ' . $request->email_type . ' para reserva ' . $reservation->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Correo enviado correctamente'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error enviando correo manual: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el correo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar formulario para crear reserva manual
     */
    public function createManualReservation()
    {
        $users = \App\Models\User::where('is_active', true)
            ->whereIn('role', ['user', 'business'])
            ->orderBy('name')
            ->get();

        $properties = \App\Models\Property::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('superadmin.reservations.create', compact('users', 'properties'));
    }

    /**
     * Obtener fechas ocupadas para una propiedad
     */
    public function getOccupiedDates(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id'
        ]);

        $occupiedDates = \App\Models\Reservation::where('property_id', $request->property_id)
            ->whereIn('status', ['pending', 'approved'])
            ->get()
            ->map(function($reservation) {
                $dates = [];
                $start = \Carbon\Carbon::parse($reservation->start_date);
                $end = \Carbon\Carbon::parse($reservation->end_date);
                
                while ($start->lte($end)) {
                    $dates[] = $start->format('Y-m-d');
                    $start->addDay();
                }
                
                return $dates;
            })
            ->flatten()
            ->unique()
            ->values()
            ->toArray();

        return response()->json([
            'occupied_dates' => $occupiedDates
        ]);
    }

    /**
     * Crear reserva manual
     */
    public function storeManualReservation(\App\Http\Requests\CreateManualReservationRequest $request)
    {
        try {
            $tempAccountService = new \App\Services\TempAccountReservationService();
            
            // Preparar datos base de la reserva
            $reservationData = [
                'property_id' => $request->property_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'guests' => $request->guests,
                'total_price' => $request->total_price,
                'special_requests' => $request->special_requests,
                'admin_notes' => $request->admin_notes,
                'status' => $request->status,
                'created_by' => auth()->id(),
                'approved_by' => $request->status === 'approved' ? auth()->id() : null,
                'approved_at' => $request->status === 'approved' ? now() : null,
                'pricing_method' => $request->pricing_method,
                'global_pricing_id' => $request->global_pricing_id ?? null,
            ];

            if ($request->client_type === 'registered') {
                // Cliente registrado - crear reserva normal
                $reservationData['user_id'] = $request->user_id;
                $reservationData['is_guest_reservation'] = false;

                // Crear reserva
                $reservation = \App\Models\Reservation::create($reservationData);

                // Registrar en auditoría
                AuditLog::log(
                    'manual_reservation_created',
                    'Reservation',
                    $reservation->id,
                    auth()->id(),
                    [
                        'user_id' => $reservation->user_id,
                        'property_id' => $reservation->property_id,
                        'status' => $reservation->status,
                        'total_price' => $reservation->total_price
                    ],
                    [],
                    'Reserva manual creada para: ' . $reservation->customer_name
                );

                // Enviar correo si está habilitado
                $emailResult = ['success' => true, 'message' => 'Correo no enviado'];
                if ($request->send_email) {
                    $emailResult = $tempAccountService->sendReservationEmail($reservation);
                }

                $successMessage = 'Reserva creada exitosamente' . 
                                ($emailResult['success'] ? ' y correo enviado al cliente' : ' pero error en correo: ' . $emailResult['message']);

            } else {
                // Cliente no registrado - crear cuenta temporal y reserva
                $guestData = [
                    'name' => $request->guest_name,
                    'email' => $request->guest_email,
                    'phone' => $request->guest_phone,
                ];

                $result = $tempAccountService->createReservationWithTempAccount(
                    $reservationData,
                    $guestData,
                    $request->send_email ?? false
                );

                if (!$result['success']) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', $result['message']);
                }

                $successMessage = $result['message'];
            }

            return redirect()->route('superadmin.reservations')
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            \Log::error('Error creando reserva manual: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear la reserva: ' . $e->getMessage());
        }
    }

    /**
     * Gestión de notificaciones
     */
    public function notifications(Request $request)
    {
        $query = \App\Models\Notification::forUser(auth()->id());

        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->unread();
            } else {
                $query->read();
            }
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('superadmin.notifications.index', compact('notifications'));
    }

    /**
     * Marcar notificación como leída
     */
    public function markNotificationAsRead(\App\Models\Notification $notification)
    {
        // Verificar que la notificación pertenece al usuario autenticado
        if ($notification->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado'
            ], 403);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notificación marcada como leída'
        ]);
    }

    /**
     * Marcar todas las notificaciones como leídas
     */
    public function markAllNotificationsAsRead()
    {
        \App\Models\Notification::forUser(auth()->id())
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Todas las notificaciones marcadas como leídas'
        ]);
    }

    /**
     * Eliminar reserva con motivo
     */
    public function deleteReservation(Request $request, \App\Models\Reservation $reservation)
    {
        $request->validate([
            'deletion_reason' => 'required|string|max:1000'
        ]);

        // Verificar que el usuario puede eliminar la reserva
        if (!$reservation->canBeDeleted()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para eliminar esta reserva'
            ], 403);
        }

        // Actualizar la reserva con la información de eliminación
        $reservation->update([
            'deletion_reason' => $request->deletion_reason,
            'deleted_by' => auth()->id(),
            'deleted_at' => now(),
            'status' => 'deleted'
        ]);

        // Registrar en auditoría
        AuditLog::log(
            'reservation_deleted',
            'Reservation',
            $reservation->id,
            auth()->id(),
            ['status' => 'deleted', 'deletion_reason' => $request->deletion_reason],
            ['status' => $reservation->getOriginal('status')],
            'Reserva eliminada: ' . $reservation->id . ' - Motivo: ' . $request->deletion_reason
        );

        // Notificar al usuario si no es una reserva de huésped
        if (!$reservation->is_guest_reservation && $reservation->user_id) {
            \App\Models\Notification::createSystemNotification(
                $reservation->user_id,
                'Reserva Eliminada',
                "Tu reserva #{$reservation->id} ha sido eliminada por un administrador. Motivo: {$request->deletion_reason}",
                ['reservation_id' => $reservation->id, 'deletion_reason' => $request->deletion_reason],
                false,
                auth()->id()
            );

            // Enviar correo de notificación
            try {
                $emailData = [
                    'user_name' => $reservation->user->name,
                    'property_title' => $reservation->property->name,
                    'property_location' => $reservation->property->location,
                    'check_in_date' => $reservation->check_in ? $reservation->check_in->format('d/m/Y') : 'N/A',
                    'check_out_date' => $reservation->check_out ? $reservation->check_out->format('d/m/Y') : 'N/A',
                    'guests' => $reservation->guests ?? 'N/A',
                    'deletion_reason' => $request->deletion_reason
                ];

                \Mail::to($reservation->user->email)->send(
                    new \App\Mail\ReservationNotification('reservation_deleted', $emailData)
                );
            } catch (\Exception $e) {
                \Log::error('Error enviando correo de eliminación: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Reserva eliminada correctamente'
        ]);
    }

    /**
     * Mostrar detalles de una membresía
     */
    public function showMembership(Membership $membership)
    {
        $membership->load(['user', 'plan']);
        
        return view('superadmin.memberships.show', compact('membership'));
    }

    /**
     * Mostrar formulario para crear nueva membresía
     */
    public function createMembership()
    {
        $users = User::where('is_active', true)->orderBy('name')->get();
        $plans = MembershipPlan::where('is_active', true)->orderBy('name')->get();
        
        return view('superadmin.memberships.create', compact('users', 'plans'));
    }

    /**
     * Almacenar nueva membresía
     */
    public function storeMembership(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'membership_plan_id' => 'required|exists:membership_plans,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,inactive,expired',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            $membership = Membership::create([
                'user_id' => $request->user_id,
                'membership_plan_id' => $request->membership_plan_id,
                'starts_at' => $request->start_date,
                'expires_at' => $request->end_date,
                'status' => $request->status,
                'notes' => $request->notes,
                'price_paid' => $request->price_paid ?? 0,
                'currency' => 'COP'
            ]);

            // Actualizar membresía actual del usuario si está activa
            if ($request->status === 'active') {
                $user = User::find($request->user_id);
                $user->update([
                    'current_membership_id' => $membership->id,
                    'membership_expires_at' => $request->end_date
                ]);
            }

            // Log de auditoría
            AuditLog::log(
                'membership_created',
                'Membership',
                $membership->id,
                auth()->id(),
                [],
                $membership->toArray(),
                'Membresía creada para usuario: ' . $membership->user->name
            );

            return redirect()->route('superadmin.memberships')
                ->with('success', 'Membresía creada correctamente');

        } catch (\Exception $e) {
            \Log::error('Error creando membresía: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al crear la membresía: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar formulario para editar membresía
     */
    public function editMembership(Membership $membership)
    {
        $users = User::where('is_active', true)->orderBy('name')->get();
        $plans = MembershipPlan::where('is_active', true)->orderBy('name')->get();
        
        return view('superadmin.memberships.edit', compact('membership', 'users', 'plans'));
    }

    /**
     * Actualizar membresía
     */
    public function updateMembership(Request $request, Membership $membership)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'membership_plan_id' => 'required|exists:membership_plans,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,inactive,expired',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            $oldData = $membership->toArray();
            
            $membership->update([
                'user_id' => $request->user_id,
                'membership_plan_id' => $request->membership_plan_id,
                'starts_at' => $request->start_date,
                'expires_at' => $request->end_date,
                'status' => $request->status,
                'notes' => $request->notes
            ]);

            // Actualizar membresía actual del usuario si está activa
            if ($request->status === 'active') {
                $user = User::find($request->user_id);
                $user->update([
                    'current_membership_id' => $membership->id,
                    'membership_expires_at' => $request->end_date
                ]);
            } elseif ($membership->user->current_membership_id == $membership->id) {
                // Si esta membresía era la actual y ya no está activa, limpiar
                $membership->user->update([
                    'current_membership_id' => null,
                    'membership_expires_at' => null
                ]);
            }

            // Log de auditoría
            AuditLog::log(
                'membership_updated',
                'Membership',
                $membership->id,
                auth()->id(),
                $oldData,
                $membership->toArray(),
                'Membresía actualizada para usuario: ' . $membership->user->name
            );

            return redirect()->route('superadmin.memberships')
                ->with('success', 'Membresía actualizada correctamente');

        } catch (\Exception $e) {
            \Log::error('Error actualizando membresía: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al actualizar la membresía: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Eliminar membresía
     */
    public function destroyMembership(Membership $membership)
    {
        try {
            $userName = $membership->user->name;
            $oldData = $membership->toArray();
            
            // Si esta membresía es la actual del usuario, limpiar
            if ($membership->user->current_membership_id == $membership->id) {
                $membership->user->update([
                    'current_membership_id' => null,
                    'membership_expires_at' => null
                ]);
            }
            
            $membership->delete();

            // Log de auditoría
            AuditLog::log(
                'membership_deleted',
                'Membership',
                $membership->id,
                auth()->id(),
                $oldData,
                [],
                'Membresía eliminada para usuario: ' . $userName
            );

            return redirect()->route('superadmin.memberships')
                ->with('success', 'Membresía eliminada correctamente');

        } catch (\Exception $e) {
            \Log::error('Error eliminando membresía: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al eliminar la membresía: ' . $e->getMessage());
        }
    }

    /**
     * Cambiar estado de membresía
     */
    public function toggleMembershipStatus(Request $request, Membership $membership)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,expired'
        ]);

        try {
            $oldStatus = $membership->status;
            $membership->update([
                'status' => $request->status,
                'updated_by' => auth()->id()
            ]);

            // Actualizar membresía actual del usuario si está activa
            if ($request->status === 'active') {
                $membership->user->update([
                    'current_membership_id' => $membership->id,
                    'membership_expires_at' => $membership->expires_at
                ]);
            } elseif ($membership->user->current_membership_id == $membership->id) {
                // Si esta membresía era la actual y ya no está activa, limpiar
                $membership->user->update([
                    'current_membership_id' => null,
                    'membership_expires_at' => null
                ]);
            }

            // Log de auditoría
            AuditLog::log(
                'membership_status_changed',
                'Membership',
                $membership->id,
                auth()->id(),
                ['old_status' => $oldStatus],
                ['new_status' => $request->status],
                'Estado de membresía cambiado de ' . $oldStatus . ' a ' . $request->status
            );

            return response()->json([
                'success' => true,
                'message' => 'Estado de membresía actualizado correctamente',
                'new_status' => $request->status
            ]);

        } catch (\Exception $e) {
            \Log::error('Error cambiando estado de membresía: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado: ' . $e->getMessage()
            ], 500);
        }
    }
}