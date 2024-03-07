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
        Schema::create('monitorings', function (Blueprint $table) {
            $table->id();
            $table->enum('type_monitor', ['HTTP(s)'])->default('HTTP(s)');
            $table->string('name');
            $table->string('url');
            $table->integer('schedule');
            $table->integer('tries')->nullable();
            $table->integer('amount_send_notification')->nullable();
            $table->enum('status_code', ['200-299', '300-399', '400-499', '500-599'])->default('200-299');
            $table->enum('notification', ['email', 'discord'])->default('email');
            $table->enum('status', ['active', 'not active', 'pause'])->default('active');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitorings');
    }
};
