<?php

namespace App\Http\Controllers;

use App\Models\GlobalPricing;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GlobalPricingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin,superadmin');
    }

    /**
     * Mostrar la página de configuración de precios globales
     */
    public function index()
    {
        $pricings = GlobalPricing::with(['creator', 'updater'])
            ->orderBy('is_active', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $activePricing = GlobalPricing::getActivePricing();
        $activePricings = GlobalPricing::getAllActivePricing();

        return view('superadmin.pricing.index', compact('pricings', 'activePricing', 'activePricings'));
    }

    /**
     * Mostrar el formulario para crear un nuevo precio global
     */
    public function create()
    {
        return view('superadmin.pricing.create');
    }

    /**
     * Almacenar un nuevo precio global
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'base_price' => 'required|numeric|min:0',
            'price_type' => 'required|in:daily,nightly',
            'has_discount' => 'boolean',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|in:percentage,fixed',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ]);

        // Si se marca como activo, desactivar otros precios
        if ($request->is_active) {
            GlobalPricing::where('is_active', true)->update(['is_active' => false]);
        }

        $pricing = GlobalPricing::create([
            'name' => $request->name,
            'base_price' => $request->base_price,
            'price_type' => $request->price_type,
            'has_discount' => $request->has_discount ?? false,
            'discount_percentage' => $request->discount_percentage,
            'discount_amount' => $request->discount_amount,
            'discount_type' => $request->discount_type,
            'description' => $request->description,
            'is_active' => $request->is_active ?? false,
            'created_by' => Auth::id()
        ]);

        // Registrar en auditoría
        AuditLog::log(
            'global_pricing_created',
            'GlobalPricing',
            $pricing->id,
            Auth::id(),
            $pricing->toArray(),
            [],
            "Precio global creado: {$pricing->name}"
        );

        return redirect()->route('superadmin.pricing')
            ->with('success', 'Precio global creado exitosamente');
    }

    /**
     * Mostrar el formulario para editar un precio global
     */
    public function edit(GlobalPricing $pricing)
    {
        return view('superadmin.pricing.edit', compact('pricing'));
    }

    /**
     * Actualizar un precio global
     */
    public function update(Request $request, GlobalPricing $pricing)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'base_price' => 'required|numeric|min:0',
            'price_type' => 'required|in:daily,nightly',
            'has_discount' => 'boolean',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|in:percentage,fixed',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ]);

        $oldData = $pricing->toArray();

        // Permitir múltiples precios activos - no desactivar otros automáticamente

        $pricing->update([
            'name' => $request->name,
            'base_price' => $request->base_price,
            'price_type' => $request->price_type,
            'has_discount' => $request->has_discount ?? false,
            'discount_percentage' => $request->discount_percentage,
            'discount_amount' => $request->discount_amount,
            'discount_type' => $request->discount_type,
            'description' => $request->description,
            'is_active' => $request->is_active ?? false,
            'updated_by' => Auth::id()
        ]);

        // Registrar en auditoría
        AuditLog::log(
            'global_pricing_updated',
            'GlobalPricing',
            $pricing->id,
            Auth::id(),
            $pricing->toArray(),
            $oldData,
            "Precio global actualizado: {$pricing->name}"
        );

        return redirect()->route('superadmin.pricing')
            ->with('success', 'Precio global actualizado exitosamente');
    }

    /**
     * Activar un precio global
     */
    public function activate(GlobalPricing $pricing)
    {
        // Activar el precio seleccionado (sin desactivar otros)
        $pricing->update([
            'is_active' => true,
            'updated_by' => Auth::id()
        ]);

        // Registrar en auditoría
        AuditLog::log(
            'global_pricing_activated',
            'GlobalPricing',
            $pricing->id,
            Auth::id(),
            ['is_active' => false],
            ['is_active' => true],
            "Precio global activado: {$pricing->name}"
        );

        return redirect()->back()
            ->with('success', 'Precio global activado exitosamente');
    }

    /**
     * Desactivar un precio global
     */
    public function deactivate(GlobalPricing $pricing)
    {
        $pricing->update([
            'is_active' => false,
            'updated_by' => Auth::id()
        ]);

        // Registrar en auditoría
        AuditLog::log(
            'global_pricing_deactivated',
            'GlobalPricing',
            $pricing->id,
            Auth::id(),
            ['is_active' => false],
            ['is_active' => true],
            "Precio global desactivado: {$pricing->name}"
        );

        return redirect()->back()
            ->with('success', 'Precio global desactivado exitosamente');
    }

    /**
     * Eliminar un precio global
     */
    public function destroy(GlobalPricing $pricing)
    {
        $pricingName = $pricing->name;
        $wasActive = $pricing->is_active;
        
        // Guardar datos para auditoría antes de eliminar
        $pricingData = $pricing->toArray();
        
        $pricing->delete();

        // Registrar en auditoría
        $action = $wasActive ? 'global_pricing_deleted_active' : 'global_pricing_deleted';
        $description = $wasActive 
            ? "Precio global activo eliminado: {$pricingName}" 
            : "Precio global eliminado: {$pricingName}";
            
        AuditLog::log(
            $action,
            'GlobalPricing',
            $pricing->id,
            Auth::id(),
            [],
            $pricingData,
            $description
        );

        return redirect()->back()
            ->with('success', 'Precio global eliminado exitosamente');
    }

    /**
     * Obtener el precio activo actual (API)
     */
    public function getActivePricing()
    {
        $pricings = GlobalPricing::getAllActivePricing();
        
        if ($pricings->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No hay precios globales activos configurados'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'pricings' => $pricings->map(function($pricing) {
                return [
                    'id' => $pricing->id,
                    'name' => $pricing->name,
                    'base_price' => $pricing->base_price,
                    'price_type' => $pricing->price_type,
                    'has_discount' => $pricing->has_discount,
                    'discount_percentage' => $pricing->discount_percentage,
                    'discount_amount' => $pricing->discount_amount,
                    'discount_type' => $pricing->discount_type,
                    'final_price' => $pricing->final_price,
                    'description' => $pricing->description
                ];
            }),
            'pricing' => $pricings->first() // Mantener compatibilidad con código existente
        ]);
    }
}
