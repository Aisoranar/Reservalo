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
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // reservation_approved, reservation_rejected, etc.
            $table->string('display_name'); // Nombre para mostrar en la interfaz
            $table->string('subject'); // Asunto del correo
            $table->text('body'); // Cuerpo del correo (HTML)
            $table->text('body_text')->nullable(); // Versión de texto plano
            $table->json('variables')->nullable(); // Variables disponibles para la plantilla
            $table->string('type'); // reservation, notification, system, etc.
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable(); // Descripción de la plantilla
            $table->timestamps();
            
            $table->index(['type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
