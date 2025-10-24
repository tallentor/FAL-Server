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
        Schema::create('case_meetings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('case_id');
            $table->unsignedBigInteger('lawyer_id');
            $table->unsignedBigInteger('user_id');
            $table->date('meeting_date');
            $table->time('meeting_time');
            $table->string('zoom_link');
            $table->timestamps();

            $table->foreign('case_id')->references('id')->on('cases')->onDelete('cascade');
            $table->foreign('lawyer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_meetings');
    }
};