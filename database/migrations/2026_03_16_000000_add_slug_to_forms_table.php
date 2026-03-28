<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Form;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->string('slug')->unique()->after('name');
        });

        // Update existing forms with unique slugs
        Form::chunk(100, function ($forms) {
            foreach ($forms as $form) {
                $slug = Str::slug($form->name);
                $count = Form::where('slug', 'like', $slug . '%')->count();
                if ($count > 0) {
                    $slug .= '-' . ($count + 1);
                }
                $form->slug = $slug;
                $form->save();
            }
        });
    }

    public function down(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};

