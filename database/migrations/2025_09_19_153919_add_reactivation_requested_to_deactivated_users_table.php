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
        Schema::table('deactivated_users', function (Blueprint $table) {
            $table->boolean('reactivation_requested')->default(false)->after('deactivation_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deactivated_users', function (Blueprint $table) {
            $table->dropColumn('reactivation_requested');
        });
    }
};
