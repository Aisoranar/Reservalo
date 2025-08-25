<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nightly_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->nullable()->constrained()->onDelete('cascade');
            $table->decimal('base_price', 10, 2); // Precio base por noche
            $table->decimal('weekend_price', 10, 2)->nullable(); // Precio especial para fines de semana
            $table->decimal('holiday_price', 10, 2)->nullable(); // Precio para días festivos
            $table->decimal('seasonal_price', 10, 2)->nullable(); // Precio por temporada
            $table->date('valid_from')->nullable(); // Fecha desde cuando es válido
            $table->date('valid_until')->nullable(); // Fecha hasta cuando es válido
            $table->boolean('is_global')->default(false); // Si se aplica a todas las propiedades
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable(); // Notas del administrador
            $table->timestamps();
            
            // Índices para optimizar consultas
            $table->index(['property_id', 'is_active']);
            $table->index(['is_global', 'is_active']);
            $table->index(['valid_from', 'valid_until']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nightly_prices');
    }
};
