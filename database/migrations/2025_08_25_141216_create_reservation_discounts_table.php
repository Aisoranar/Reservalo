<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservation_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->onDelete('cascade');
            $table->foreignId('discount_id')->constrained()->onDelete('cascade');
            $table->decimal('original_amount', 10, 2); // Monto original antes del descuento
            $table->decimal('discount_amount', 10, 2); // Monto del descuento aplicado
            $table->decimal('final_amount', 10, 2); // Monto final después del descuento
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable(); // Notas del administrador
            $table->foreignId('applied_by')->nullable()->constrained('users')->onDelete('set null'); // Quién aplicó el descuento
            $table->timestamp('applied_at')->nullable(); // Cuándo se aplicó
            $table->timestamps();
            
            // Índices
            $table->index(['reservation_id', 'status']);
            $table->index(['discount_id', 'status']);
            $table->index(['applied_by', 'applied_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservation_discounts');
    }
};
