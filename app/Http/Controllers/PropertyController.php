<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $query = Property::active()->with(['primaryImage', 'city.department', 'reviews']);

        // Búsqueda por texto
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhereHas('city', function($cityQuery) use ($search) {
                      $cityQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('city.department', function($deptQuery) use ($search) {
                      $deptQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filtros básicos
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('department')) {
            $query->whereHas('city', function($q) use ($request) {
                $q->where('department_id', $request->department);
            });
        }

        if ($request->filled('city')) {
            $query->where('city_id', $request->city);
        }

        if ($request->filled('min_price') && $request->filled('max_price')) {
            $query->byPriceRange($request->min_price, $request->max_price);
        }

        // Filtros adicionales
        if ($request->filled('min_bedrooms')) {
            $query->where('bedrooms', '>=', $request->min_bedrooms);
        }

        if ($request->filled('min_bathrooms')) {
            $query->where('bathrooms', '>=', $request->min_bathrooms);
        }

        if ($request->filled('min_capacity')) {
            $query->where('capacity', '>=', $request->min_capacity);
        }

        if ($request->filled('amenities') && is_array($request->amenities)) {
            foreach ($request->amenities as $amenity) {
                $query->whereJsonContains('amenities', $amenity);
            }
        }

        if ($request->filled('pet_friendly')) {
            $query->where('pet_friendly', $request->pet_friendly === 'true');
        }

        if ($request->filled('parking')) {
            $query->where('parking', $request->parking);
        }

        // Ordenamiento
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating', 'desc');
                break;
            case 'featured':
                $query->where('featured_until', '>', now())->orderBy('featured_until', 'desc');
                break;
            default:
                $query->latest();
        }

        $properties = $query->paginate(12)->withQueryString();

        // Calcular precios efectivos para cada propiedad
        $pricingService = new \App\Services\PricingService();
        $activeGlobalPricing = \App\Models\GlobalPricing::getActivePricing();
        
        $properties->getCollection()->transform(function ($property) use ($pricingService) {
            $property->effective_price = $pricingService->getNightlyPrice($property, now());
            return $property;
        });

        // Obtener estadísticas para filtros usando precios efectivos
        $effectivePrices = $properties->getCollection()->pluck('effective_price');
        $stats = [
            'total_properties' => Property::active()->count(),
            'price_range' => [
                'min' => $effectivePrices->min() ?? 0,
                'max' => $effectivePrices->max() ?? 1000
            ],
            'types' => Property::active()->distinct('type')->pluck('type'),
            'amenities' => Property::active()->whereNotNull('amenities')->get()->pluck('amenities')->flatten()->unique()->take(10),
            'active_global_pricing' => $activeGlobalPricing
        ];

        return view('properties.index', compact('properties', 'stats'));
    }

    public function show(Property $property)
    {
        $property->load(['images', 'reservations' => function($query) {
            $query->whereIn('status', ['pending', 'approved', 'rejected']);
        }]);
        
        // Get approved reservations for the calendar
        $approvedReservations = $property->reservations->where('status', 'approved');
        
        // Cargar precios por noche
        $property->load('nightlyPrices');
        
        // Obtener precio global activo
        $activeGlobalPricing = \App\Models\GlobalPricing::getActivePricing();
        
        // Calcular precio efectivo usando PricingService
        $pricingService = new \App\Services\PricingService();
        $effectivePrice = $pricingService->getNightlyPrice($property, now());
        
        return view('properties.show', compact('property', 'approvedReservations', 'activeGlobalPricing', 'effectivePrice'));
    }

    public function quickView(Property $property)
    {
        try {
            // Información básica sin cargar relaciones complejas
            $data = [
                'id' => $property->id,
                'name' => $property->name,
                'description' => $property->description,
                'price' => '$' . number_format($property->price, 0),
                'location' => $property->location,
                'capacity' => $property->capacity,
                'bedrooms' => $property->bedrooms ?? 'N/A',
                'bathrooms' => $property->bathrooms ?? 'N/A',
                'type' => $property->type,
                'image_url' => null,
                'amenities' => [],
                'rating' => 0,
                'review_count' => 0
            ];
            
            // Intentar cargar la ciudad si existe
            if ($property->city_id) {
                try {
                    $property->load('city.department');
                    if ($property->city && $property->city->department) {
                        $data['location'] = $property->city->name . ', ' . $property->city->department->name;
                    }
                } catch (\Exception $e) {
                    // Si falla, mantener la ubicación original
                }
            }
            
            // Intentar cargar la imagen principal
            try {
                $primaryImage = $property->images()->where('is_primary', true)->first();
                if ($primaryImage) {
                    $data['image_url'] = $primaryImage->full_url;
                }
            } catch (\Exception $e) {
                // Si falla, mantener image_url como null
            }
            
            // Intentar cargar amenities si existen
            try {
                if ($property->amenities && is_array($property->amenities)) {
                    $data['amenities'] = array_slice($property->amenities, 0, 5);
                }
            } catch (\Exception $e) {
                // Si falla, mantener amenities como array vacío
            }
            
            // Intentar cargar rating y review_count
            try {
                $data['rating'] = $property->rating;
                $data['review_count'] = $property->review_count;
            } catch (\Exception $e) {
                // Si falla, mantener valores por defecto
            }
            
            return response()->json($data);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al cargar la información de la propiedad',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function create()
    {
        return view('admin.properties.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'type' => 'required|string|in:casa,apartamento,cabaña,hotel,finca',
            'services' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $property = Property::create($request->except('images'));

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('properties', 'public');
                
                PropertyImage::create([
                    'property_id' => $property->id,
                    'url' => $path,
                    'alt_text' => $property->name,
                    'is_primary' => $index === 0,
                    'order' => $index
                ]);
            }
        }

        return redirect()->route('admin.properties.index')
            ->with('success', 'Propiedad creada exitosamente');
    }

    public function edit(Property $property)
    {
        $property->load('images');
        return view('admin.properties.edit', compact('property'));
    }

    public function update(Request $request, Property $property)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'type' => 'required|string|in:casa,apartamento,cabaña,hotel,finca',
            'services' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $property->update($request->except('images'));

        if ($request->hasFile('images')) {
            // Eliminar imágenes existentes
            foreach ($property->images as $image) {
                Storage::disk('public')->delete($image->url);
                $image->delete();
            }

            // Subir nuevas imágenes
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('properties', 'public');
                
                PropertyImage::create([
                    'property_id' => $property->id,
                    'url' => $path,
                    'alt_text' => $property->name,
                    'is_primary' => $index === 0,
                    'order' => $index
                ]);
            }
        }

        return redirect()->route('admin.properties.index')
            ->with('success', 'Propiedad actualizada exitosamente');
    }

    public function destroy(Property $property)
    {
        // Eliminar imágenes
        foreach ($property->images as $image) {
            Storage::disk('public')->delete($image->url);
        }

        $property->delete();

        return redirect()->route('admin.properties.index')
            ->with('success', 'Propiedad eliminada exitosamente');
    }

    public function toggleStatus(Property $property)
    {
        $property->update(['is_active' => !$property->is_active]);

        $status = $property->is_active ? 'activada' : 'desactivada';
        return back()->with('success', "Propiedad {$status} exitosamente");
    }

    /**
     * Obtener fechas ocupadas para una propiedad
     */
    public function getOccupiedDates(Property $property)
    {
        $occupiedDates = \App\Models\Reservation::where('property_id', $property->id)
            ->whereIn('status', ['pending', 'approved'])
            ->get()
            ->map(function($reservation) {
                $dates = [];
                $start = \Carbon\Carbon::parse($reservation->start_date);
                $end = \Carbon\Carbon::parse($reservation->end_date);
                
                while ($start->lte($end)) {
                    $dates[] = $start->format('Y-m-d');
                    $start->addDay();
                }
                
                return $dates;
            })
            ->flatten()
            ->unique()
            ->values()
            ->toArray();

        return response()->json([
            'occupied_dates' => $occupiedDates
        ]);
    }
}
