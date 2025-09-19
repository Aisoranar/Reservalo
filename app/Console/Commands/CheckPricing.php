<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\GlobalPricing;
use App\Models\Property;
use App\Services\PricingService;

class CheckPricing extends Command
{
    protected $signature = 'pricing:check {property_id?}';
    protected $description = 'Verifica el sistema de precios y muestra el precio activo';

    public function handle()
    {
        $this->info('🔍 Verificando sistema de precios...');
        $this->newLine();

        // Verificar precio global activo
        $activePricing = GlobalPricing::getActivePricing();
        
        if ($activePricing) {
            $this->info("✅ Precio global activo encontrado:");
            $this->line("   📋 Nombre: {$activePricing->name}");
            $this->line("   💰 Precio base: $" . number_format($activePricing->base_price, 0));
            $this->line("   🎯 Precio final: $" . number_format($activePricing->final_price, 0));
            $this->line("   📅 Tipo: {$activePricing->price_type}");
            $this->line("   🔄 Activo: " . ($activePricing->is_active ? 'Sí' : 'No'));
            
            if ($activePricing->has_discount) {
                $this->line("   🏷️ Descuento: " . 
                    ($activePricing->discount_type === 'percentage' 
                        ? $activePricing->discount_percentage . '%' 
                        : '$' . number_format($activePricing->discount_amount, 0)));
            }
        } else {
            $this->error("❌ No hay precio global activo configurado");
        }

        $this->newLine();

        // Verificar precios de propiedades
        $propertyId = $this->argument('property_id');
        
        if ($propertyId) {
            $property = Property::find($propertyId);
            if ($property) {
                $this->info("🏠 Verificando propiedad: {$property->name}");
                
                $pricingService = new PricingService();
                $effectivePrice = $pricingService->getNightlyPrice($property, now());
                
                $this->line("   💰 Precio base de la propiedad: $" . number_format($property->price, 0));
                $this->line("   🎯 Precio efectivo calculado: $" . number_format($effectivePrice, 0));
                
                if ($effectivePrice != $property->price) {
                    $this->warn("   ⚠️ El precio efectivo es diferente al precio base de la propiedad");
                } else {
                    $this->info("   ✅ El precio efectivo coincide con el precio base");
                }
            } else {
                $this->error("❌ Propiedad con ID {$propertyId} no encontrada");
            }
        } else {
            $this->info("💡 Usa 'php artisan pricing:check {property_id}' para verificar una propiedad específica");
        }

        $this->newLine();
        
        // Mostrar estadísticas
        $totalGlobalPricings = GlobalPricing::count();
        $activeGlobalPricings = GlobalPricing::where('is_active', true)->count();
        
        $this->info("📊 Estadísticas del sistema de precios:");
        $this->line("   📋 Total de precios globales: {$totalGlobalPricings}");
        $this->line("   ✅ Precios activos: {$activeGlobalPricings}");

        return Command::SUCCESS;
    }
}