<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Property;
use App\Models\Favorite;

echo "=== Test de Favoritos ===\n";

// Verificar usuarios
$users = User::all();
echo "Usuarios encontrados: " . $users->count() . "\n";

if ($users->count() > 0) {
    $user = $users->first();
    echo "Usuario de prueba: " . $user->name . " (ID: " . $user->id . ")\n";
    
    // Verificar propiedades
    $properties = Property::all();
    echo "Propiedades encontradas: " . $properties->count() . "\n";
    
    if ($properties->count() > 0) {
        $property = $properties->first();
        echo "Propiedad de prueba: " . $property->name . " (ID: " . $property->id . ")\n";
        
        // Crear favorito
        try {
            $favorite = Favorite::create([
                'user_id' => $user->id,
                'property_id' => $property->id
            ]);
            echo "✅ Favorito creado exitosamente: ID " . $favorite->id . "\n";
            
            // Verificar que se creó
            $favoritesCount = Favorite::count();
            echo "Total de favoritos en la BD: " . $favoritesCount . "\n";
            
            // Verificar relación
            $userFavorites = $user->favorites()->count();
            echo "Favoritos del usuario: " . $userFavorites . "\n";
            
            $propertyFavorites = $property->favorites()->count();
            echo "Favoritos de la propiedad: " . $propertyFavorites . "\n";
            
        } catch (Exception $e) {
            echo "❌ Error al crear favorito: " . $e->getMessage() . "\n";
        }
    } else {
        echo "❌ No hay propiedades en la base de datos\n";
    }
} else {
    echo "❌ No hay usuarios en la base de datos\n";
}

echo "=== Fin del Test ===\n";
