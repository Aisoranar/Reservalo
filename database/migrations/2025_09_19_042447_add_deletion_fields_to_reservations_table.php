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
            $table->text('deletion_reason')->nullable()->after('rejection_reason');
            $table->unsignedBigInteger('deleted_by')->nullable()->after('deletion_reason');
            $table->timestamp('deleted_at')->nullable()->after('deleted_by');
            
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['deleted_by']);
            $table->dropColumn(['deletion_reason', 'deleted_by', 'deleted_at']);
        });
    }
};
