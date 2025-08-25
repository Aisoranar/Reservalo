<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre del descuento (ej: "Descuento por estadía larga")
            $table->string('code')->nullable()->unique(); // Código promocional opcional
            $table->enum('type', ['percentage', 'fixed_amount']); // Tipo de descuento
            $table->decimal('value', 8, 2); // Porcentaje o monto fijo
            $table->enum('application', ['automatic', 'manual', 'conditional']); // Cómo se aplica
            $table->integer('min_nights')->nullable(); // Noches mínimas para aplicar
            $table->integer('max_nights')->nullable(); // Noches máximas para aplicar
            $table->decimal('min_amount', 10, 2)->nullable(); // Monto mínimo de reserva
            $table->date('valid_from')->nullable(); // Fecha de inicio de validez
            $table->date('valid_until')->nullable(); // Fecha de fin de validez
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable(); // Descripción del descuento
            $table->text('terms_conditions')->nullable(); // Términos y condiciones
            $table->timestamps();
            
            // Índices
            $table->index(['type', 'is_active']);
            $table->index(['application', 'is_active']);
            $table->index(['valid_from', 'valid_until']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
