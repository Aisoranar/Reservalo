<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMustChangePassword
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->must_change_password) {
            // Excluir rutas relacionadas con cambio de contraseña y logout
            $excludedRoutes = [
                'password.change',
                'password.update',
                'logout',
                'password.request',
                'password.email',
                'password.reset'
            ];
            
            if (!in_array($request->route()->getName(), $excludedRoutes)) {
                return redirect()->route('password.change')
                    ->with('warning', 'Debes cambiar tu contraseña temporal antes de continuar.');
            }
        }

        return $next($request);
    }
}
