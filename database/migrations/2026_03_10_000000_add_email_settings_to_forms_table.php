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
        Schema::table('forms', function (Blueprint $table) {
            $table->boolean('enable_email_notification')->default(false)->after('status');
            $table->string('email_subject')->nullable()->after('enable_email_notification');
            $table->text('email_template')->nullable()->after('email_subject');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->dropColumn(['enable_email_notification', 'email_subject', 'email_template']);
        });
    }
};