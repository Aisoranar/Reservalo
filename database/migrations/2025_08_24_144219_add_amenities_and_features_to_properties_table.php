<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            // Amenities y características
            $table->json('amenities')->nullable()->comment('Amenities como WiFi, piscina, etc.');
            $table->json('features')->nullable()->comment('Características como vista al mar, montaña, etc.');
            $table->integer('bedrooms')->default(1)->comment('Número de habitaciones');
            $table->integer('bathrooms')->default(1)->comment('Número de baños');
            $table->decimal('size', 8, 2)->nullable()->comment('Tamaño en metros cuadrados');
            $table->string('parking')->default('no')->comment('Tipo de estacionamiento');
            $table->boolean('pet_friendly')->default(false)->comment('Permite mascotas');
            $table->boolean('smoking_allowed')->default(false)->comment('Permite fumar');
            $table->string('check_in_time')->default('15:00')->comment('Hora de check-in');
            $table->string('check_out_time')->default('11:00')->comment('Hora de check-out');
            $table->integer('min_stay')->default(1)->comment('Estadía mínima en noches');
            $table->decimal('cleaning_fee', 8, 2)->nullable()->comment('Tarifa de limpieza');
            $table->decimal('security_deposit', 8, 2)->nullable()->comment('Depósito de seguridad');
            $table->text('house_rules')->nullable()->comment('Reglas de la casa');
            $table->text('cancellation_policy')->nullable()->comment('Política de cancelación');
            $table->decimal('rating', 3, 2)->default(0.00)->comment('Calificación promedio');
            $table->integer('review_count')->default(0)->comment('Número de reseñas');
            $table->string('status')->default('available')->comment('Estado: available, booked, maintenance');
            $table->timestamp('featured_until')->nullable()->comment('Hasta cuándo está destacada');
            
            // Índices para mejor rendimiento
            $table->index(['status', 'is_active']);
            $table->index(['rating', 'review_count']);
            $table->index(['featured_until', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropIndex(['status', 'is_active']);
            $table->dropIndex(['rating', 'review_count']);
            $table->dropIndex(['featured_until', 'is_active']);
            
            $table->dropColumn([
                'amenities', 'features', 'bedrooms', 'bathrooms', 'size',
                'parking', 'pet_friendly', 'smoking_allowed', 'check_in_time',
                'check_out_time', 'min_stay', 'cleaning_fee', 'security_deposit',
                'house_rules', 'cancellation_policy', 'rating', 'review_count',
                'status', 'featured_until'
            ]);
        });
    }
};
