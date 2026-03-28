<?php

namespace Database\Seeders;

use App\Models\Form;
use App\Models\Store;
use App\Models\FormStore;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FormStoreSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Skip if data exists (called twice)
        if (FormStore::count() > 0) {
            echo "FormStore data already exists. Skipping...\n";
            return;
        }

        $forms = Form::all();
        $stores = Store::all();

        if ($forms->isEmpty() || $stores->isEmpty()) {
            echo "No forms or stores found.\n";
            return;
        }

        $links = [
            ['form_name' => 'Survei Kepuasan Pelanggan', 'store_name' => 'Toko A'],
            ['form_name' => 'Survei Kepuasan Pelanggan', 'store_name' => 'Toko B'],
            ['form_name' => 'Survei Kepuasan Pelanggan', 'store_name' => 'Toko C']
        ];

        foreach ($links as $link) {
            $form = $forms->firstWhere('name', $link['form_name']);
            $store = $stores->firstWhere('name', $link['store_name']);

            if ($form && $store && !FormStore::where('form_id', $form->id)->where('store_id', $store->id)->exists()) {
                $name = Str::slug($store->name . ' ' . $form->name, '_');
                $slug = Str::slug($store->name . '_' . $form->name, '_');
                
                $baseSlug = $slug;
                $counter = 1;
                while (FormStore::where('custom_url_slug', $slug)->exists()) {
                    $slug = $baseSlug . '_' . $counter++;
                }

                FormStore::create([
                    'form_id' => $form->id,
                    'store_id' => $store->id,
                    'name' => $name,
                    'custom_url_slug' => $slug,
                ]);

                echo "Created: {$name} (slug: {$slug})\n";
            }
        }
    }
}

