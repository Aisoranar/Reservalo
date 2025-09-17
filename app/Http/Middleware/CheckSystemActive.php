<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\SystemSetting;

class CheckSystemActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si el sistema está activo
        $isSystemActive = SystemSetting::get('site_active', true);
        
        if (!$isSystemActive) {
            // Si el usuario es superadmin, permitir acceso
            if (auth()->check() && auth()->user()->isSuperAdmin()) {
                return $next($request);
            }
            
            return response()->view('maintenance', [], 503);
        }

        // Verificar modo mantenimiento
        $isMaintenanceMode = SystemSetting::get('maintenance_mode', false);
        
        if ($isMaintenanceMode) {
            // Si el usuario es superadmin o admin, permitir acceso
            if (auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->isAdmin())) {
                return $next($request);
            }
            
            return response()->view('maintenance', [], 503);
        }

        return $next($request);
    }
}
