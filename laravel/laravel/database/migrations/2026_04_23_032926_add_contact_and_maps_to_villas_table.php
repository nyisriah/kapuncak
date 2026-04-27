<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('villas', function (Blueprint $table) {
            $table->string('contact_pengelola_villa')->nullable()->after('name');
            $table->string('google_maps_link')->nullable()->after('contact_pengelola_villa');
        });
    }

    public function down(): void
    {
        Schema::table('villas', function (Blueprint $table) {
            $table->dropColumn(['contact_pengelola_villa', 'google_maps_link']);
        });
    }
};