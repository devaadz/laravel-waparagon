<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            if (!Schema::hasColumn('forms', 'enable_whatsapp_image')) {
                $table->boolean('enable_whatsapp_image')->default(false)->after('whatsapp_template');
            }
            if (!Schema::hasColumn('forms', 'whatsapp_image')) {
                $table->string('whatsapp_image')->nullable()->after('enable_whatsapp_image');
            }
        });
    }

    public function down(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->dropColumn(['enable_whatsapp_image', 'whatsapp_image']);
        });
    }
};

