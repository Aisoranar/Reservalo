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
        Schema::table('users', function (Blueprint $table) {
            // Actualizar el campo role para incluir superadmin
            $table->dropColumn('role');
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['superadmin', 'admin', 'user'])->default('user')->after('whatsapp');
            
            // Campos para membresías (sin foreign key por ahora)
            $table->unsignedBigInteger('current_membership_id')->nullable();
            $table->timestamp('membership_expires_at')->nullable();
            $table->boolean('membership_notification_sent')->default(false);
            
            // Campos adicionales para superadmin
            $table->boolean('can_manage_system')->default(false);
            $table->boolean('can_manage_memberships')->default(false);
            
            // Índices
            $table->index(['role', 'is_active']);
            $table->index(['membership_expires_at', 'is_active']);
        });
        
        // Agregar foreign key después de crear la tabla
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('current_membership_id')->references('id')->on('memberships')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Eliminar foreign key primero
            $table->dropForeign(['current_membership_id']);
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role', 'is_active']);
            $table->dropIndex(['membership_expires_at', 'is_active']);
            
            $table->dropColumn([
                'role',
                'current_membership_id',
                'membership_expires_at',
                'membership_notification_sent',
                'can_manage_system',
                'can_manage_memberships'
            ]);
        });
        
        // Restaurar el campo role original
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['user', 'admin'])->default('user')->after('whatsapp');
        });
    }
};
