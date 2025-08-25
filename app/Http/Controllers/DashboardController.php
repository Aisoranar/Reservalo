<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Reservation;
use App\Models\Favorite;
use App\Models\PropertyReview;
use App\Models\Property;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Obtener estadísticas del usuario
        $stats = [
            'reservations' => Reservation::where('user_id', $user->id)->count(),
            'favorites' => Favorite::where('user_id', $user->id)->count(),
            'reviews' => PropertyReview::where('user_id', $user->id)->count(),
            'properties_viewed' => 0, // Por ahora en 0, se puede implementar tracking después
        ];
        
        // Obtener reservas recientes
        $recentReservations = Reservation::where('user_id', $user->id)
            ->with(['property', 'property.images'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Obtener favoritos recientes
        $recentFavorites = Favorite::where('user_id', $user->id)
            ->with(['property', 'property.images'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Obtener reseñas recientes
        $recentReviews = PropertyReview::where('user_id', $user->id)
            ->with(['property', 'property.images'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Obtener propiedades destacadas (para sugerir exploración)
        $featuredProperties = Property::where('is_active', true)
            ->where('featured_until', '>', now())
            ->with(['images', 'city'])
            ->take(3)
            ->get();
        
        return view('dashboard', compact(
            'stats',
            'recentReservations',
            'recentFavorites',
            'recentReviews',
            'featuredProperties'
        ));
    }
}
