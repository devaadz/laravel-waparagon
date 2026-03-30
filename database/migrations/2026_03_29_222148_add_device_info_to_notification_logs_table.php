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
        Schema::table('notification_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('whatsapp_device_id')->nullable()->after('response_id');
            $table->string('device_name')->nullable()->after('whatsapp_device_id');
            $table->string('device_system')->nullable()->after('device_name');
            $table->foreign('whatsapp_device_id')->references('id')->on('whatsapp_devices')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notification_logs', function (Blueprint $table) {
            $table->dropForeign(['whatsapp_device_id']);
            $table->dropColumn(['whatsapp_device_id', 'device_name', 'device_system']);
        });
    }
};
