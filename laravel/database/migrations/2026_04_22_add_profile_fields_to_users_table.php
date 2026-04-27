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
            // Add avatar field for profile picture
            $table->string('avatar')->nullable()->after('email_verified_at');
            
            // Add whatsapp_number field
            $table->string('whatsapp_number')->nullable()->after('avatar');
            
            // Add address field
            $table->text('address')->nullable()->after('whatsapp_number');

            // Phone field should already be in model, just ensure it's in migration
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('address');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'avatar',
                'whatsapp_number',
                'address',
            ]);
            
            // Only drop phone if it was added by this migration
            if (Schema::hasColumn('users', 'phone')) {
                $table->dropColumn('phone');
            }
        });
    }
};
