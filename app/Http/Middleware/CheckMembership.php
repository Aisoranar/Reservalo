<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMembership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $action = 'access'): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Superadmin y admin no necesitan membresía
        if ($user->isSuperAdmin() || $user->isAdmin()) {
            return $next($request);
        }

        // Verificar si tiene membresía activa
        if (!$user->hasActiveMembership()) {
            return redirect()->route('membership.required')
                ->with('error', 'Necesitas una membresía activa para acceder a esta funcionalidad.');
        }

        // Verificar permisos específicos según la acción
        switch ($action) {
            case 'create_property':
                if (!$user->canCreateProperty()) {
                    return redirect()->route('membership.upgrade')
                        ->with('error', 'Tu plan actual no permite crear más propiedades.');
                }
                break;
                
            case 'create_reservation':
                if (!$user->canCreateReservation()) {
                    return redirect()->route('membership.upgrade')
                        ->with('error', 'Tu plan actual no permite crear más reservas.');
                }
                break;
        }

        return $next($request);
    }
}
