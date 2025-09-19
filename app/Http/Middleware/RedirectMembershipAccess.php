<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectMembershipAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        // Solo los admins y superadmins pueden acceder a gestión de membresías
        if (!$user->isAdmin() && !$user->isSuperAdmin()) {
            return redirect()->route('dashboard')
                ->with('info', 'La gestión de membresías es solo para administradores.');
        }

        return $next($request);
    }
}
