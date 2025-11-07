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
        Schema::create('appointment_notifications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('lawyer_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->onDelete('cascade');

            $table->string('lawyer_name')->nullable();
            $table->string('client_name')->nullable();

            $table->string('type')->nullable();
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->string('zoom_link')->nullable();

            $table->enum('status', ['unread', 'read'])->default('unread');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_notifications');
    }
};
