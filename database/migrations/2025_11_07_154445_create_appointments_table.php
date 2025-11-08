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
        Schema::create('appointments', function (Blueprint $table) {
           $table->id();

            // client who books the appointment
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // lawyer (taken from users table)
            $table->foreignId('lawyer_id')->constrained('users')->onDelete('cascade');

            $table->date('appointment_date');
            $table->time('appointment_time');

            // case fields
            $table->string('full_name');
            $table->string('case_title');
            $table->text('case_description');
            $table->string('type_of_visa')->nullable();
            $table->string('country_of_destination')->nullable();
            $table->string('current_visa_status')->nullable();
            $table->date('visa_expiry_date')->nullable();
            $table->text('immigration_history')->nullable();
            $table->string('reason_for_immigration');
            $table->string('previous_visa_denials')->nullable();
            $table->integer('number_of_dependents')->nullable();
            $table->text('additional_notes')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
