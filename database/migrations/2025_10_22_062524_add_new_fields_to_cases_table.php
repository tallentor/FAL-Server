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
        Schema::table('cases', function (Blueprint $table) {
            $table->string('full_name')->after('user_id');
            $table->string('current_visa_status')->nullable()->after('country_of_destination');
            $table->text('previous_visa_denials')->nullable()->after('reason_for_immigration');
            $table->integer('number_of_dependents')->nullable()->after('previous_visa_denials');
            $table->text('additional_notes')->nullable()->after('number_of_dependents');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cases', function (Blueprint $table) {
            $table->dropColumn([
                'full_name',
                'current_visa_status',
                'previous_visa_denials',
                'number_of_dependents',
                'additional_notes',
            ]);
        });
    }
};
