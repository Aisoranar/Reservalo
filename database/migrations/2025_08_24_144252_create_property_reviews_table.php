<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('reservation_id')->nullable()->constrained()->onDelete('set null');
            
            // Calificaciones por categoría
            $table->integer('overall_rating')->comment('Calificación general 1-5');
            $table->integer('cleanliness_rating')->comment('Limpieza 1-5');
            $table->integer('communication_rating')->comment('Comunicación 1-5');
            $table->integer('check_in_rating')->comment('Check-in 1-5');
            $table->integer('accuracy_rating')->comment('Precisión 1-5');
            $table->integer('location_rating')->comment('Ubicación 1-5');
            $table->integer('value_rating')->comment('Valor 1-5');
            
            $table->text('comment')->nullable()->comment('Comentario del usuario');
            $table->text('host_response')->nullable()->comment('Respuesta del anfitrión');
            $table->timestamp('host_response_at')->nullable()->comment('Cuándo respondió el anfitrión');
            $table->boolean('is_verified')->default(false)->comment('Reseña verificada');
            $table->boolean('is_helpful')->default(false)->comment('Marcada como útil');
            $table->integer('helpful_count')->default(0)->comment('Número de votos útiles');
            $table->timestamps();
            
            // Índices
            $table->index(['property_id', 'overall_rating']);
            $table->index(['user_id', 'created_at']);
            $table->unique(['property_id', 'user_id', 'reservation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_reviews');
    }
};
