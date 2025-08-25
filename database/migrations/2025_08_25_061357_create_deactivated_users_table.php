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
        Schema::create('deactivated_users', function (Blueprint $table) {
            $table->id();
            
            // Información básica del usuario
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            
            // Campos adicionales del usuario
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->text('bio')->nullable();
            $table->string('profile_picture')->nullable();
            
            // Información de la cuenta
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('is_active')->default(false);
            $table->enum('account_type', ['regular', 'premium', 'business'])->default('regular');
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            
            // Metadatos de desactivación
            $table->enum('deactivation_reason', [
                'user_request', 
                'inactivity', 
                'policy_violation', 
                'suspicious_activity',
                'temporary_hold',
                'other'
            ])->default('user_request');
            $table->text('deactivation_notes')->nullable();
            $table->timestamp('deactivated_at');
            $table->string('deactivated_by')->nullable(); // Puede ser 'self' o ID de admin
            $table->timestamp('reactivation_requested_at')->nullable();
            $table->text('reactivation_reason')->nullable();
            
            // Campos de auditoría
            $table->json('deactivation_data')->nullable(); // Datos relacionados al momento de desactivación
            $table->string('deactivation_ip')->nullable();
            $table->text('deactivation_user_agent')->nullable();
            
            $table->timestamps();
            
            // Índices para búsquedas eficientes
            $table->index(['email', 'deactivated_at']);
            $table->index(['deactivation_reason', 'deactivated_at']);
            $table->index(['is_active', 'deactivated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deactivated_users');
    }
};
