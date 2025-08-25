<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\NightlyPrice;
use App\Models\Discount;
use App\Models\ReservationDiscount;
use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class PricingController extends Controller
{
    protected $pricingService;

    public function __construct(PricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }

    /**
     * Mostrar el dashboard de precios
     */
    public function index()
    {
        $stats = $this->pricingService->getPricingStats();
        $properties = Property::with('nightlyPrices')->get();
        $discounts = Discount::with('reservationDiscounts')->get();
        
        return view('admin.pricing.index', compact('stats', 'properties', 'discounts'));
    }

    /**
     * Mostrar formulario para crear precio por noche
     */
    public function createNightlyPrice()
    {
        $properties = Property::all();
        return view('admin.pricing.create-nightly-price', compact('properties'));
    }

    /**
     * Guardar nuevo precio por noche
     */
    public function storeNightlyPrice(Request $request)
    {
        $request->validate([
            'property_id' => 'nullable|exists:properties,id',
            'base_price' => 'required|numeric|min:0',
            'weekend_price' => 'nullable|numeric|min:0',
            'holiday_price' => 'nullable|numeric|min:0',
            'seasonal_price' => 'nullable|numeric|min:0',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
            'is_global' => 'boolean',
            'notes' => 'nullable|string|max:500'
        ]);

        $nightlyPrice = NightlyPrice::create($request->all());

        return redirect()->route('admin.pricing.index')
            ->with('success', 'Precio por noche creado exitosamente');
    }

    /**
     * Mostrar formulario para editar precio por noche
     */
    public function editNightlyPrice(NightlyPrice $nightlyPrice)
    {
        $properties = Property::all();
        return view('admin.pricing.edit-nightly-price', compact('nightlyPrice', 'properties'));
    }

    /**
     * Actualizar precio por noche
     */
    public function updateNightlyPrice(Request $request, NightlyPrice $nightlyPrice)
    {
        $request->validate([
            'property_id' => 'nullable|exists:properties,id',
            'base_price' => 'required|numeric|min:0',
            'weekend_price' => 'nullable|numeric|min:0',
            'holiday_price' => 'nullable|numeric|min:0',
            'seasonal_price' => 'nullable|numeric|min:0',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
            'is_global' => 'boolean',
            'notes' => 'nullable|string|max:500'
        ]);

        $nightlyPrice->update($request->all());

        return redirect()->route('admin.pricing.index')
            ->with('success', 'Precio por noche actualizado exitosamente');
    }

    /**
     * Eliminar precio por noche
     */
    public function destroyNightlyPrice(NightlyPrice $nightlyPrice)
    {
        $nightlyPrice->delete();

        return redirect()->route('admin.pricing.index')
            ->with('success', 'Precio por noche eliminado exitosamente');
    }

    /**
     * Mostrar formulario para crear descuento
     */
    public function createDiscount()
    {
        return view('admin.pricing.create-discount');
    }

    /**
     * Guardar nuevo descuento
     */
    public function storeDiscount(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:discounts',
            'type' => 'required|in:percentage,fixed_amount',
            'value' => 'required|numeric|min:0',
            'application' => 'required|in:automatic,manual,conditional',
            'min_nights' => 'nullable|integer|min:1',
            'max_nights' => 'nullable|integer|min:1|gte:min_nights',
            'min_amount' => 'nullable|numeric|min:0',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
            'description' => 'nullable|string|max:1000',
            'terms_conditions' => 'nullable|string|max:1000'
        ]);

        Discount::create($request->all());

        return redirect()->route('admin.pricing.index')
            ->with('success', 'Descuento creado exitosamente');
    }

    /**
     * Mostrar formulario para editar descuento
     */
    public function editDiscount(Discount $discount)
    {
        return view('admin.pricing.edit-discount', compact('discount'));
    }

    /**
     * Actualizar descuento
     */
    public function updateDiscount(Request $request, Discount $discount)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:discounts,code,' . $discount->id,
            'type' => 'required|in:percentage,fixed_amount',
            'value' => 'required|numeric|min:0',
            'application' => 'required|in:automatic,manual,conditional',
            'min_nights' => 'nullable|integer|min:1',
            'max_nights' => 'nullable|integer|min:1|gte:min_nights',
            'min_amount' => 'nullable|numeric|min:0',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
            'description' => 'nullable|string|max:1000',
            'terms_conditions' => 'nullable|string|max:1000'
        ]);

        $discount->update($request->all());

        return redirect()->route('admin.pricing.index')
            ->with('success', 'Descuento actualizado exitosamente');
    }

    /**
     * Eliminar descuento
     */
    public function destroyDiscount(Discount $discount)
    {
        $discount->delete();

        return redirect()->route('admin.pricing.index')
            ->with('success', 'Descuento eliminado exitosamente');
    }

    /**
     * Aplicar descuento a una reserva específica
     */
    public function applyDiscountToReservation(Request $request): JsonResponse
    {
        $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'discount_id' => 'required|exists:discounts,id',
            'admin_notes' => 'nullable|string|max:500'
        ]);

        try {
            $result = $this->pricingService->applyManualDiscount(
                $request->reservation_id,
                $request->discount_id,
                $request->admin_notes
            );

            return response()->json([
                'success' => true,
                'message' => 'Descuento aplicado exitosamente',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al aplicar descuento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calcular precio para fechas específicas
     */
    public function calculatePrice(Request $request): JsonResponse
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'check_in' => 'required|date|after:today',
            'check_out' => 'required|date|after:check_in'
        ]);

        $property = Property::findOrFail($request->property_id);
        $checkIn = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);

        $pricing = $this->pricingService->calculateReservationPrice($property, $checkIn, $checkOut);

        return response()->json([
            'success' => true,
            'data' => $pricing
        ]);
    }

    /**
     * Obtener descuentos disponibles para una reserva
     */
    public function getAvailableDiscounts(Request $request): JsonResponse
    {
        $request->validate([
            'nights' => 'required|integer|min:1',
            'total_amount' => 'required|numeric|min:0',
            'check_in' => 'required|date'
        ]);

        $checkIn = Carbon::parse($request->check_in);
        $availableDiscounts = $this->pricingService->getAvailableDiscounts(
            $request->nights,
            $request->total_amount,
            $checkIn
        );

        return response()->json([
            'success' => true,
            'data' => $availableDiscounts
        ]);
    }

    /**
     * Cambiar estado de un descuento
     */
    public function toggleDiscountStatus(Discount $discount): JsonResponse
    {
        $discount->update(['is_active' => !$discount->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Estado del descuento actualizado',
            'is_active' => $discount->is_active
        ]);
    }

    /**
     * Cambiar estado de un precio por noche
     */
    public function toggleNightlyPriceStatus(NightlyPrice $nightlyPrice): JsonResponse
    {
        $nightlyPrice->update(['is_active' => !$nightlyPrice->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Estado del precio actualizado',
            'is_active' => $nightlyPrice->is_active
        ]);
    }
}
