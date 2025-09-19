<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\AuditLog;
use Symfony\Component\HttpFoundation\Response;

class AuditLogging
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Solo registrar para usuarios autenticados y rutas específicas
        if (auth()->check() && $this->shouldLog($request)) {
            $this->logRequest($request, $response);
        }

        return $response;
    }

    /**
     * Determinar si la solicitud debe ser registrada
     */
    private function shouldLog(Request $request): bool
    {
        $route = $request->route();
        if (!$route) return false;

        $routeName = $route->getName();
        $method = $request->method();

        // Solo registrar rutas específicas y métodos que modifican datos
        $loggableRoutes = [
            'superadmin.*',
            'admin.*',
            'membership.*',
            'profile.*'
        ];

        $loggableMethods = ['POST', 'PUT', 'PATCH', 'DELETE'];

        // Verificar si la ruta debe ser registrada
        foreach ($loggableRoutes as $pattern) {
            if (str_contains($routeName, str_replace('*', '', $pattern))) {
                return in_array($method, $loggableMethods);
            }
        }

        return false;
    }

    /**
     * Registrar la solicitud en auditoría
     */
    private function logRequest(Request $request, Response $response): void
    {
        $route = $request->route();
        $routeName = $route->getName();
        $method = $request->method();

        // Determinar el tipo de acción
        $action = $this->getActionFromRoute($routeName, $method);

        // Obtener datos relevantes
        $data = $this->getRelevantData($request, $response);

        // Registrar en auditoría
        AuditLog::log(
            $action,
            $this->getModelType($routeName),
            $this->getModelId($request),
            auth()->id(),
            $data['old_values'] ?? null,
            $data['new_values'] ?? null,
            $this->getDescription($routeName, $method, $data)
        );
    }

    /**
     * Obtener el tipo de acción desde la ruta
     */
    private function getActionFromRoute(string $routeName, string $method): string
    {
        $actions = [
            'POST' => 'created',
            'PUT' => 'updated',
            'PATCH' => 'updated',
            'DELETE' => 'deleted'
        ];

        $baseAction = $actions[$method] ?? 'accessed';

        // Acciones específicas por ruta
        if (str_contains($routeName, 'settings')) {
            return 'settings_updated';
        } elseif (str_contains($routeName, 'toggle')) {
            return 'toggled';
        } elseif (str_contains($routeName, 'reset')) {
            return 'reset';
        }

        return $baseAction;
    }

    /**
     * Obtener el tipo de modelo desde la ruta
     */
    private function getModelType(string $routeName): ?string
    {
        if (str_contains($routeName, 'users')) return 'User';
        if (str_contains($routeName, 'roles')) return 'Role';
        if (str_contains($routeName, 'permissions')) return 'Permission';
        if (str_contains($routeName, 'membership-plans')) return 'MembershipPlan';
        if (str_contains($routeName, 'memberships')) return 'Membership';
        if (str_contains($routeName, 'settings')) return 'SystemSetting';
        if (str_contains($routeName, 'properties')) return 'Property';
        if (str_contains($routeName, 'reservations')) return 'Reservation';

        return null;
    }

    /**
     * Obtener el ID del modelo desde la solicitud
     */
    private function getModelId(Request $request): ?int
    {
        // Intentar obtener el ID directamente
        $id = $request->route('id');
        if ($id && is_numeric($id)) {
            return (int) $id;
        }

        // Verificar si hay un modelo resuelto automáticamente
        $user = $request->route('user');
        if ($user) {
            if (is_object($user)) {
                return $user->id ? (int) $user->id : null;
            }
            return is_numeric($user) ? (int) $user : null;
        }

        $role = $request->route('role');
        if ($role) {
            if (is_object($role)) {
                return $role->id ? (int) $role->id : null;
            }
            return is_numeric($role) ? (int) $role : null;
        }

        $membership = $request->route('membership');
        if ($membership) {
            if (is_object($membership)) {
                return $membership->id ? (int) $membership->id : null;
            }
            return is_numeric($membership) ? (int) $membership : null;
        }

        return null;
    }

    /**
     * Obtener datos relevantes para la auditoría
     */
    private function getRelevantData(Request $request, Response $response): array
    {
        $data = [];

        // Para configuraciones, obtener valores antes y después
        if (str_contains($request->route()->getName(), 'settings')) {
            $data['new_values'] = $request->except(['_token', '_method']);
        }

        return $data;
    }

    /**
     * Generar descripción de la acción
     */
    private function getDescription(string $routeName, string $method, array $data): string
    {
        $descriptions = [
            'superadmin.settings.update' => 'Configuraciones del sistema actualizadas',
            'superadmin.toggle-system' => 'Estado del sistema modificado',
            'superadmin.toggle-maintenance' => 'Modo mantenimiento modificado',
            'superadmin.settings.reset' => 'Configuraciones restauradas a valores por defecto',
            'superadmin.users' => 'Gestión de usuarios accedida',
            'superadmin.roles' => 'Gestión de roles accedida',
            'superadmin.permissions' => 'Gestión de permisos accedida',
            'superadmin.memberships' => 'Gestión de membresías accedida',
            'superadmin.reports' => 'Reportes accedidos',
            'superadmin.audit-logs' => 'Logs de auditoría accedidos'
        ];

        return $descriptions[$routeName] ?? "Acción {$method} en {$routeName}";
    }
}
