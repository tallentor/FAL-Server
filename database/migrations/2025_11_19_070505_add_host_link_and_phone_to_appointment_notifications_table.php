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
        Schema::table('appointment_notifications', function (Blueprint $table) {
            $table->text('host_zoom_link')->nullable()->after('zoom_link');
            $table->string('user_phone_number')->nullable()->after('client_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointment_notifications', function (Blueprint $table) {
            $table->dropColumn(['host_zoom_link', 'user_phone_number']);
        });
    }
};