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
        // For MySQL/MariaDB - modify enum to include 'cancelled'
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'paid', 'confirmed', 'cancelled') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert enum to original values
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'paid', 'confirmed') NOT NULL DEFAULT 'pending'");
    }
};
