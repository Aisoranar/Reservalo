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
            // Campos de desactivación
            $table->boolean('is_active')->default(true)->after('role');
            $table->timestamp('deactivated_at')->nullable()->after('is_active');
            $table->enum('deactivation_reason', [
                'user_request', 
                'inactivity', 
                'policy_violation', 
                'suspicious_activity',
                'temporary_hold',
                'other'
            ])->nullable()->after('deactivated_at');
            $table->text('deactivation_notes')->nullable()->after('deactivation_reason');
            $table->timestamp('reactivated_at')->nullable()->after('deactivation_notes');
            
            // Campos adicionales del perfil
            $table->string('address')->nullable()->after('whatsapp');
            $table->string('city')->nullable()->after('address');
            $table->string('state')->nullable()->after('city');
            $table->string('country')->nullable()->after('state');
            $table->string('postal_code')->nullable()->after('country');
            $table->date('birth_date')->nullable()->after('postal_code');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('birth_date');
            $table->text('bio')->nullable()->after('gender');
            $table->string('profile_picture')->nullable()->after('bio');
            $table->enum('account_type', ['regular', 'premium', 'business'])->default('regular')->after('profile_picture');
            $table->timestamp('last_login_at')->nullable()->after('account_type');
            $table->string('last_login_ip')->nullable()->after('last_login_at');
            
            // Índices para búsquedas eficientes
            $table->index(['is_active', 'deactivated_at']);
            $table->index(['deactivation_reason', 'deactivated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'deactivated_at']);
            $table->dropIndex(['deactivation_reason', 'deactivated_at']);
            
            $table->dropColumn([
                'is_active',
                'deactivated_at',
                'deactivation_reason',
                'deactivation_notes',
                'reactivated_at',
                'address',
                'city',
                'state',
                'country',
                'postal_code',
                'birth_date',
                'gender',
                'bio',
                'profile_picture',
                'account_type',
                'last_login_at',
                'last_login_ip',
            ]);
        });
    }
};
