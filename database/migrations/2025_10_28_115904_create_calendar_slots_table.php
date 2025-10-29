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
       Schema::create('calendar_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Calendar::class)->constrained()->cascadeOnDelete();
            $table->date('day');
            $table->time('slot_1')->nullable();
            $table->time('slot_2')->nullable();
            $table->time('slot_3')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_slots');
    }
};
