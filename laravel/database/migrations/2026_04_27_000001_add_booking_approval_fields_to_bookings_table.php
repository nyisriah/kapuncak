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
        Schema::table('bookings', function (Blueprint $table) {
            // Add approval timestamp (nullable for backward compatibility)
            $table->timestamp('approved_at')->nullable()->after('status');
            
            // Add payment deadline (nullable for backward compatibility)
            $table->timestamp('payment_deadline')->nullable()->after('approved_at');
            
            // Add indexes for performance
            $table->index('approved_at');
            $table->index('payment_deadline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['approved_at']);
            $table->dropIndex(['payment_deadline']);
            $table->dropColumn(['approved_at', 'payment_deadline']);
        });
    }
};
