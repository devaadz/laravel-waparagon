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
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->string('device_id')->index();
            $table->string('message_id')->unique();
            $table->string('from_phone')->index();
            $table->string('to_phone')->nullable();
            $table->text('text')->nullable();
            $table->string('type')->default('text'); // text, image, video, audio, file, etc
            $table->string('media_url')->nullable();
            $table->json('payload')->nullable();
            $table->boolean('is_from_me')->default(false);
            $table->timestamp('message_timestamp')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
    }
};