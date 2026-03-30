<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->text('privacy_policy')->nullable()->after('whatsapp_image');
            $table->enum('language', ['id', 'en'])->default('id')->after('privacy_policy');
        });
    }

    public function down(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->dropColumn('privacy_policy');
            $table->dropColumn('language');
        });
    }
};
