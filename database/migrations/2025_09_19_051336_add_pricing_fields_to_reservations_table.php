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
            $table->enum('pricing_method', ['global', 'manual'])->default('manual')->after('total_price');
            $table->unsignedBigInteger('global_pricing_id')->nullable()->after('pricing_method');
            $table->json('pricing_details')->nullable()->after('global_pricing_id');
            
            $table->foreign('global_pricing_id')->references('id')->on('global_pricings')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['global_pricing_id']);
            $table->dropColumn(['pricing_method', 'global_pricing_id', 'pricing_details']);
        });
    }
};
