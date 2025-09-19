<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Actualizar el ENUM de account_type para incluir 'individual'
        DB::statement("ALTER TABLE users MODIFY COLUMN account_type ENUM('regular', 'premium', 'business', 'individual') DEFAULT 'regular'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir a los valores originales
        DB::statement("ALTER TABLE users MODIFY COLUMN account_type ENUM('regular', 'premium', 'business') DEFAULT 'regular'");
    }
};
