<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Property;
use App\Models\PropertyImage;

class PropertyImagesSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener todas las propiedades
        $properties = Property::all();
        
        if ($properties->isEmpty()) {
            $this->command->warn('No hay propiedades disponibles. Ejecuta primero SamplePropertiesSeeder.');
            return;
        }

        // Imágenes disponibles para cada propiedad
        $images = [
            'src/image1.jpg',
            'src/image2.jpg', 
            'src/image3.jpg'
        ];

        // Textos alternativos para las imágenes
        $altTexts = [
            'Vista frontal de la propiedad',
            'Interior de la propiedad',
            'Vista exterior de la propiedad'
        ];

        foreach ($properties as $property) {
            // Crear 3 imágenes para cada propiedad
            for ($i = 0; $i < 3; $i++) {
                PropertyImage::create([
                    'property_id' => $property->id,
                    'url' => $images[$i],
                    'alt_text' => $altTexts[$i],
                    'is_primary' => $i === 0, // La primera imagen es la principal
                    'order' => $i + 1
                ]);
            }
        }

        $this->command->info('✅ Imágenes de propiedades creadas exitosamente');
        $this->command->info("📸 Se crearon " . ($properties->count() * 3) . " imágenes para " . $properties->count() . " propiedades");
    }
}
