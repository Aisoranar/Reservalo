<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Property;
use App\Models\PropertyImage;

class CheckPropertyImages extends Command
{
    protected $signature = 'properties:check-images';
    protected $description = 'Verificar que las imágenes de las propiedades estén funcionando correctamente';

    public function handle()
    {
        $this->info('🔍 Verificando imágenes de propiedades...');
        
        $properties = Property::with('images')->get();
        
        if ($properties->isEmpty()) {
            $this->error('❌ No hay propiedades en la base de datos');
            return;
        }
        
        $this->info("📊 Total de propiedades: " . $properties->count());
        $this->info("📸 Total de imágenes: " . PropertyImage::count());
        
        $this->newLine();
        
        foreach ($properties as $property) {
            $this->info("🏠 Propiedad: {$property->name}");
            $this->info("   ID: {$property->id}");
            $this->info("   Imágenes: {$property->images->count()}");
            
            if ($property->images->count() > 0) {
                foreach ($property->images as $image) {
                    $this->line("     - {$image->url} (Principal: " . ($image->is_primary ? 'Sí' : 'No') . ")");
                    $this->line("       URL completa: {$image->full_url}");
                }
            } else {
                $this->warn("     ⚠️  No tiene imágenes");
            }
            
            $this->newLine();
        }
        
        $this->info('✅ Verificación completada');
    }
}
