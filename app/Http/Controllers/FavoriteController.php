<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Agregar una propiedad a favoritos
     */
    public function toggle(Request $request, Property $property)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Debes iniciar sesión para agregar favoritos'
            ], 401);
        }

        $existingFavorite = Favorite::where('user_id', $user->id)
            ->where('property_id', $property->id)
            ->first();

        if ($existingFavorite) {
            // Remover de favoritos
            $existingFavorite->delete();
            $isFavorite = false;
            $message = 'Propiedad removida de favoritos';
        } else {
            // Agregar a favoritos
            Favorite::create([
                'user_id' => $user->id,
                'property_id' => $property->id
            ]);
            $isFavorite = true;
            $message = 'Propiedad agregada a favoritos';
        }

        return response()->json([
            'success' => true,
            'isFavorite' => $isFavorite,
            'message' => $message
        ]);
    }

    /**
     * Verificar si una propiedad está en favoritos
     */
    public function check(Request $request, Property $property)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['isFavorite' => false]);
        }

        $isFavorite = Favorite::where('user_id', $user->id)
            ->where('property_id', $property->id)
            ->exists();

        return response()->json(['isFavorite' => $isFavorite]);
    }

    /**
     * Obtener lista de favoritos del usuario
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $favorites = $user->favorites()->with([
            'property.city.department',
            'property.reviews' => function($query) {
                $query->select('id', 'property_id', 'overall_rating');
            },
            'property.primaryImage'
        ])->paginate(12);

        return view('favorites.index', compact('favorites'));
    }
}
