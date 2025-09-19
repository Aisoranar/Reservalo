<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckTempAccountSecurity
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Si es una cuenta temporal, verificar seguridad adicional
            if ($user->must_change_password && $user->temp_password) {
                // Verificar que no haya pasado mucho tiempo desde la creación
                $accountAge = $user->created_at->diffInDays(now());
                
                if ($accountAge > 7) {
                    // Cuenta temporal muy antigua - forzar cambio de contraseña
                    Log::warning('Cuenta temporal antigua detectada', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'account_age_days' => $accountAge,
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent()
                    ]);
                    
                    return redirect()->route('password.change')
                        ->with('warning', 'Por seguridad, debes cambiar tu contraseña temporal. Tu cuenta temporal es muy antigua.');
                }
                
                // Verificar actividad sospechosa
                if ($this->hasSuspiciousActivity($user, $request)) {
                    Log::warning('Actividad sospechosa detectada en cuenta temporal', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'route' => $request->route()->getName()
                    ]);
                    
                    return redirect()->route('password.change')
                        ->with('warning', 'Por seguridad, debes cambiar tu contraseña temporal. Se detectó actividad sospechosa.');
                }
            }
        }

        return $next($request);
    }

    /**
     * Verificar actividad sospechosa
     */
    private function hasSuspiciousActivity($user, Request $request): bool
    {
        // Verificar IP diferente a la de creación
        $creationIp = $user->created_at->format('Y-m-d H:i:s');
        
        // Verificar User-Agent muy diferente
        $userAgent = $request->userAgent();
        if (strlen($userAgent) < 10) {
            return true; // User-Agent muy corto es sospechoso
        }
        
        // Verificar si está intentando acceder a rutas administrativas
        $adminRoutes = ['superadmin', 'admin', 'manage'];
        $currentRoute = $request->route()->getName() ?? '';
        
        foreach ($adminRoutes as $adminRoute) {
            if (str_contains($currentRoute, $adminRoute)) {
                return true;
            }
        }
        
        return false;
    }
}
