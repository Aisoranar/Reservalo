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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // reservation_approval, system_alert, etc.
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable(); // Datos adicionales
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Usuario que recibe la notificación
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null'); // Usuario que creó la notificación
            $table->foreignId('related_id')->nullable(); // ID del objeto relacionado (reserva, etc.)
            $table->string('related_type')->nullable(); // Tipo del objeto relacionado
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->boolean('is_urgent')->default(false);
            $table->timestamps();
            
            $table->index(['user_id', 'is_read']);
            $table->index(['type', 'is_read']);
            $table->index(['related_id', 'related_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
