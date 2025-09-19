<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('global_pricings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nombre del precio global (ej: Precio Estándar)');
            $table->decimal('base_price', 10, 2)->comment('Precio base por día/noche');
            $table->enum('price_type', ['daily', 'nightly'])->default('daily')->comment('Tipo de precio: diario o nocturno');
            $table->boolean('has_discount')->default(false)->comment('Si tiene descuento aplicado');
            $table->decimal('discount_percentage', 5, 2)->nullable()->comment('Porcentaje de descuento (0-100)');
            $table->decimal('discount_amount', 10, 2)->nullable()->comment('Monto fijo de descuento');
            $table->enum('discount_type', ['percentage', 'fixed'])->nullable()->comment('Tipo de descuento: porcentaje o monto fijo');
            $table->boolean('is_active')->default(true)->comment('Si el precio está activo');
            $table->text('description')->nullable()->comment('Descripción del precio');
            $table->unsignedBigInteger('created_by')->nullable()->comment('Usuario que creó el precio');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Usuario que actualizó el precio');
            $table->timestamps();
            
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_pricings');
    }
};
