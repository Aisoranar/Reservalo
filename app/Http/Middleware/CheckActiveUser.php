<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckActiveUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Verificar si el usuario está desactivado
            if ($user->isDeactivated()) {
                // Si está desactivado por violaciones de políticas, redirigir a página de suspensión
                if ($user->isSuspended()) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    
                    return redirect()->route('login')
                        ->withErrors(['email' => 'Tu cuenta ha sido suspendida. Contacta a soporte para más información.']);
                }
                
                // Si está desactivado por solicitud propia, mostrar página de reactivación
                if ($user->deactivation_reason === 'user_request') {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    
                    return redirect()->route('login')
                        ->with('status', 'Tu cuenta está desactivada. Inicia sesión para reactivarla.');
                }
                
                // Para otros motivos, redirigir a página de reactivación
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('login')
                    ->with('status', 'Tu cuenta está temporalmente desactivada. Inicia sesión para reactivarla.');
            }
        }

        return $next($request);
    }
}
