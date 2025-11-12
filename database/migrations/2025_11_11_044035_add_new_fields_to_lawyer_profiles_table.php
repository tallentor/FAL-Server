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
        Schema::table('lawyer_profiles', function (Blueprint $table) {
            $table->string('id_or_passport')->nullable();
            $table->string('proof_of_authorisation')->nullable();
            $table->string('bar_association_id')->nullable();
            $table->string('cv')->nullable();
            $table->string('signed_agreement')->nullable();
            $table->text('areas_of_practice')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lawyer_profiles', function (Blueprint $table) {
            //
        });
    }
};
