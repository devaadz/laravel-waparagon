<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Store;
use App\Models\Form;
use App\Models\FormField;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create dummy stores
        $stores = [
            ['name' => 'Toko A', 'address' => 'Jakarta'],
            ['name' => 'Toko B', 'address' => 'Bandung'],
            ['name' => 'Toko C', 'address' => 'Surabaya'],
        ];

        foreach ($stores as $storeData) {
            Store::create($storeData);
        }

        // Create dummy form
$form = Form::create([
            'name' => 'Survei Kepuasan Pelanggan',
            'slug' => 'survei-kepuasan-pelanggan',
            'description' => 'Form survei untuk mengukur kepuasan pelanggan di berbagai toko',
            'status' => 'active',
        ]);

        $this->call(FormStoreSeeder::class);

        // Create dummy fields
        $fields = [
            [
                'label' => 'Nama Lengkap',
                'type' => 'text',
                'placeholder' => 'Masukkan nama lengkap Anda',
                'required' => true,
                'sort_order' => 1,
            ],
            [
                'label' => 'Alamat',
                'type' => 'textarea',
                'placeholder' => 'Masukkan alamat lengkap',
                'required' => true,
                'sort_order' => 2,
            ],
            [
                'label' => 'Nomor Telepon',
                'type' => 'tel',
                'placeholder' => '08123456789',
                'required' => true,
                'sort_order' => 3,
            ],
            [
                'label' => 'Email',
                'type' => 'email',
                'placeholder' => 'email@example.com',
                'required' => false,
                'sort_order' => 4,
            ],
            [
                'label' => 'Tanggal Kunjungan',
                'type' => 'date',
                'required' => true,
                'sort_order' => 5,
            ],
            [
                'label' => 'Rating Kepuasan',
                'type' => 'radio',
                'required' => true,
                'options' => ['Sangat Puas', 'Puas', 'Cukup', 'Kurang Puas', 'Tidak Puas'],
                'sort_order' => 6,
            ],
            [
                'label' => 'Produk yang Dibeli',
                'type' => 'select',
                'required' => false,
                'options' => ['Elektronik', 'Pakaian', 'Makanan', 'Kosmetik', 'Lainnya'],
                'sort_order' => 7,
            ],
            [
                'label' => 'Saran dan Kritik',
                'type' => 'textarea',
                'placeholder' => 'Berikan saran atau kritik Anda',
                'required' => false,
                'sort_order' => 8,
            ],
        ];

        foreach ($fields as $fieldData) {
            FormField::create(array_merge($fieldData, ['form_id' => $form->id]));
        }

        $this->call(FormStoreSeeder::class);
    }
}

