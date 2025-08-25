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

        // Obtener estadísticas para filtros
        $stats = [
            'total_properties' => Property::active()->count(),
            'price_range' => [
                'min' => Property::active()->min('price') ?? 0,
                'max' => Property::active()->max('price') ?? 1000
            ],
            'types' => Property::active()->distinct('type')->pluck('type'),
            'amenities' => Property::active()->whereNotNull('amenities')->get()->pluck('amenities')->flatten()->unique()->take(10)
        ];

        return view('properties.index', compact('properties', 'stats'));
    }

    public function show(Property $property)
    {
        $property->load(['images', 'reservations.approved()']);
        
        return view('properties.show', compact('property'));
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
}
