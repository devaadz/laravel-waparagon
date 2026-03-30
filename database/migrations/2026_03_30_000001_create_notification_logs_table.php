<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('form_id');
            $table->foreign('form_id')->references('id')->on('forms')->onDelete('cascade');
            $table->foreignId('response_id')->constrained('responses')->onDelete('cascade');
            $table->enum('type', ['email', 'whatsapp']); // email atau whatsapp
            $table->string('recipient')->nullable(); // email atau nomor WA
            $table->text('message')->nullable();
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            
            $table->index(['form_id', 'created_at']);
            $table->index(['type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
