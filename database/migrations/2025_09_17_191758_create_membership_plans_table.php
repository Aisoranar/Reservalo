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
        Schema::create('membership_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ej: "Plan Básico", "Plan Premium"
            $table->string('slug')->unique(); // Ej: "basic", "premium"
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('currency', 3)->default('COP');
            $table->integer('duration_days'); // Duración en días
            $table->json('features')->nullable(); // Características del plan
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->integer('max_properties')->nullable(); // Máximo de propiedades
            $table->integer('max_reservations')->nullable(); // Máximo de reservas
            $table->json('permissions')->nullable(); // Permisos específicos del plan
            $table->timestamps();
            
            $table->index(['is_active', 'is_default']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_plans');
    }
};
