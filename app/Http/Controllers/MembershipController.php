<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\MembershipPlan;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MembershipController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Mostrar planes de membresía disponibles
     */
    public function index()
    {
        $plans = MembershipPlan::active()->orderBy('price')->get();
        $userMembership = auth()->user()->getActiveMembership();

        return view('membership.index', compact('plans', 'userMembership'));
    }

    /**
     * Mostrar información de un plan específico
     */
    public function show(MembershipPlan $plan)
    {
        $userMembership = auth()->user()->getActiveMembership();
        
        return view('membership.show', compact('plan', 'userMembership'));
    }

    /**
     * Crear nueva membresía
     */
    public function create(Request $request, MembershipPlan $plan)
    {
        $user = auth()->user();

        // Verificar si ya tiene una membresía activa
        if ($user->hasActiveMembership()) {
            return back()->withErrors(['error' => 'Ya tienes una membresía activa.']);
        }

        try {
            DB::beginTransaction();

            // Crear membresía
            $membership = $user->createMembership($plan, [
                'price_paid' => $plan->price,
                'currency' => $plan->currency,
                'notes' => 'Membresía creada por el usuario'
            ]);

            // Log de auditoría
            AuditLog::log(
                'created',
                'Membership',
                $membership->id,
                $user->id,
                null,
                $membership->toArray(),
                'Nueva membresía creada'
            );

            DB::commit();

            return redirect()->route('membership.success', $membership)
                ->with('success', 'Membresía creada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear membresía', [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'error' => $e->getMessage()
            ]);

            return back()->withErrors(['error' => 'Error al crear la membresía.']);
        }
    }

    /**
     * Página de éxito después de crear membresía
     */
    public function success(Membership $membership)
    {
        if ($membership->user_id !== auth()->id()) {
            abort(403);
        }

        return view('membership.success', compact('membership'));
    }

    /**
     * Extender membresía actual
     */
    public function extend(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365'
        ]);

        $user = auth()->user();
        $days = $request->days;

        if (!$user->hasActiveMembership()) {
            return back()->withErrors(['error' => 'No tienes una membresía activa para extender.']);
        }

        try {
            DB::beginTransaction();

            $oldExpiry = $user->currentMembership->expires_at;
            $user->extendMembership($days);
            $newExpiry = $user->currentMembership->fresh()->expires_at;

            // Log de auditoría
            AuditLog::log(
                'updated',
                'Membership',
                $user->currentMembership->id,
                $user->id,
                ['expires_at' => $oldExpiry],
                ['expires_at' => $newExpiry],
                "Membresía extendida por {$days} días"
            );

            DB::commit();

            return back()->with('success', "Membresía extendida exitosamente por {$days} días.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al extender membresía', [
                'user_id' => $user->id,
                'days' => $days,
                'error' => $e->getMessage()
            ]);

            return back()->withErrors(['error' => 'Error al extender la membresía.']);
        }
    }

    /**
     * Cancelar membresía actual
     */
    public function cancel(Request $request)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500'
        ]);

        $user = auth()->user();

        if (!$user->hasActiveMembership()) {
            return back()->withErrors(['error' => 'No tienes una membresía activa para cancelar.']);
        }

        try {
            DB::beginTransaction();

            $membership = $user->currentMembership;
            $user->cancelMembership($request->reason);

            // Log de auditoría
            AuditLog::log(
                'cancelled',
                'Membership',
                $membership->id,
                $user->id,
                ['status' => 'active'],
                ['status' => 'cancelled'],
                'Membresía cancelada por el usuario'
            );

            DB::commit();

            return back()->with('success', 'Membresía cancelada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al cancelar membresía', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return back()->withErrors(['error' => 'Error al cancelar la membresía.']);
        }
    }

    /**
     * Historial de membresías del usuario
     */
    public function history()
    {
        $memberships = auth()->user()->memberships()
            ->with('plan:id,name,slug')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('membership.history', compact('memberships'));
    }

    /**
     * Página de membresía requerida
     */
    public function required()
    {
        $plans = MembershipPlan::active()->orderBy('price')->get();
        
        return view('membership.required', compact('plans'));
    }

    /**
     * Página de actualización de membresía
     */
    public function upgrade()
    {
        $plans = MembershipPlan::active()->orderBy('price')->get();
        $userMembership = auth()->user()->getActiveMembership();
        
        return view('membership.upgrade', compact('plans', 'userMembership'));
    }

    /**
     * Cambiar a un plan diferente
     */
    public function changePlan(Request $request, MembershipPlan $plan)
    {
        $user = auth()->user();

        if (!$user->hasActiveMembership()) {
            return back()->withErrors(['error' => 'No tienes una membresía activa para cambiar.']);
        }

        if ($user->currentMembership->plan_id === $plan->id) {
            return back()->withErrors(['error' => 'Ya tienes este plan activo.']);
        }

        try {
            DB::beginTransaction();

            // Cancelar membresía actual
            $oldMembership = $user->currentMembership;
            $user->cancelMembership('Cambio de plan');

            // Crear nueva membresía
            $newMembership = $user->createMembership($plan, [
                'price_paid' => $plan->price,
                'currency' => $plan->currency,
                'notes' => 'Cambio de plan de membresía'
            ]);

            // Log de auditoría
            AuditLog::log(
                'updated',
                'Membership',
                $newMembership->id,
                $user->id,
                ['plan_id' => $oldMembership->plan_id],
                ['plan_id' => $plan->id],
                'Plan de membresía cambiado'
            );

            DB::commit();

            return redirect()->route('membership.success', $newMembership)
                ->with('success', 'Plan de membresía cambiado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al cambiar plan de membresía', [
                'user_id' => $user->id,
                'old_plan_id' => $user->currentMembership->plan_id,
                'new_plan_id' => $plan->id,
                'error' => $e->getMessage()
            ]);

            return back()->withErrors(['error' => 'Error al cambiar el plan de membresía.']);
        }
    }
}
