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
        Schema::create('case_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id');
            $table->unsignedBigInteger('lawyer_id');
            $table->unsignedBigInteger('case_id');
            $table->string('lawyer_name');
            $table->string('client_name');
            $table->enum('type', ['approved', 'rejected']);
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->string('zoom_link')->nullable();
            $table->string('status')->default('unread');
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('lawyer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('case_id')->references('id')->on('cases')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_notifications');
    }
};
