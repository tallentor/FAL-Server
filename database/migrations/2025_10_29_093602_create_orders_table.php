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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // customer
            $table->foreignId('lawyer_id')->constrained('users')->onDelete('cascade'); // lawyer
            $table->foreignId('lawyer_profile_id')->constrained('lawyer_profiles')->onDelete('cascade'); // specific lawyer profile
            $table->decimal('lawyer_fee', 10, 2);
            $table->decimal('system_fee', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');
            $table->string('payhere_order_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
