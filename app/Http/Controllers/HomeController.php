<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Services\PricingService;

class HomeController extends Controller
{
    public function index()
    {
        // Obtener propiedades destacadas
        $featuredProperties = Property::active()
            ->where('featured_until', '>', now())
            ->with(['images', 'city.department', 'reviews'])
            ->take(6)
            ->get();

        // Si no hay propiedades destacadas, obtener las más recientes
        if ($featuredProperties->isEmpty()) {
            $featuredProperties = Property::active()
                ->with(['images', 'city.department', 'reviews'])
                ->latest()
                ->take(6)
                ->get();
        }

        // Calcular precios efectivos para cada propiedad
        $pricingService = new PricingService();
        $activeGlobalPricing = \App\Models\GlobalPricing::getActivePricing();
        
        $featuredProperties->transform(function ($property) use ($pricingService) {
            $property->effective_price = $pricingService->getNightlyPrice($property, now());
            return $property;
        });

        // Obtener estadísticas generales
        $stats = [
            'total_properties' => Property::active()->count(),
            'featured_properties' => $featuredProperties->count(),
            'active_global_pricing' => $activeGlobalPricing
        ];

        return view('home', compact('featuredProperties', 'stats'));
    }
}