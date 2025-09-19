<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\GlobalPricing;

class CheckGlobalPricing extends Command
{
    protected $signature = 'pricing:global';
    protected $description = 'Muestra todos los precios globales y cuál está activo';

    public function handle()
    {
        $this->info('🔍 Verificando precios globales...');
        $this->newLine();

        $pricings = GlobalPricing::orderBy('is_active', 'desc')->orderBy('created_at', 'desc')->get();

        if ($pricings->isEmpty()) {
            $this->error('❌ No hay precios globales configurados');
            return Command::SUCCESS;
        }

        $this->info('📋 Precios globales disponibles:');
        $this->newLine();

        foreach ($pricings as $pricing) {
            $status = $pricing->is_active ? '✅ ACTIVO' : '❌ Inactivo';
            $this->line("ID: {$pricing->id} | {$status}");
            $this->line("   📋 Nombre: {$pricing->name}");
            $this->line("   💰 Precio base: $" . number_format($pricing->base_price, 0));
            $this->line("   🎯 Precio final: $" . number_format($pricing->final_price, 0));
            $this->line("   📅 Tipo: {$pricing->price_type}");
            
            if ($pricing->has_discount) {
                $discount = $pricing->discount_type === 'percentage' 
                    ? $pricing->discount_percentage . '%' 
                    : '$' . number_format($pricing->discount_amount, 0);
                $this->line("   🏷️ Descuento: {$discount}");
            }
            
            $this->line("   📝 Descripción: {$pricing->description}");
            $this->line("   📅 Creado: {$pricing->created_at->format('d/m/Y H:i')}");
            $this->newLine();
        }

        $activeCount = $pricings->where('is_active', true)->count();
        $this->info("📊 Resumen:");
        $this->line("   Total de precios: {$pricings->count()}");
        $this->line("   Precios activos: {$activeCount}");

        return Command::SUCCESS;
    }
}