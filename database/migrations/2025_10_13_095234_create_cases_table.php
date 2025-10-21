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
        Schema::create('cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type_of_visa')->nullable();
            $table->string('country_of_destination')->nullable();
            $table->date('visa_expiry_date')->nullable();
            $table->text('immigration_history')->nullable();
            $table->string('case_title');
            $table->text('case_description');
            $table->string('reason_for_immigration');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cases');
    }
};
