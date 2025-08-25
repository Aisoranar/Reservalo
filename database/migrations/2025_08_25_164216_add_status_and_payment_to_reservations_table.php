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
        Schema::table('reservations', function (Blueprint $table) {
            // Agregar nuevos valores al enum status existente
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending')->change();
            
            // Agregar campos de pago
            $table->enum('payment_status', ['pending', 'paid', 'partial', 'refunded'])->default('pending');
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->text('admin_notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'payment_status', 
                'amount_paid', 
                'admin_notes', 
                'approved_at', 
                'paid_at', 
                'approved_by'
            ]);
            
            // Revertir status a valores originales
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->change();
        });
    }
};
