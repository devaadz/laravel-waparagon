@extends('admin.layout')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <h1 class="text-3xl font-bold mb-6">Dokumentasi Sistem Form Dinamis WA Paragon</h1>

    <div class="prose max-w-none">
        <h2>1. Pendahuluan</h2>
        <p>Sistem Form Dinamis WA Paragon adalah aplikasi Laravel yang memungkinkan admin untuk membuat form survei dinamis dengan field yang dapat ditambah/dikurang secara fleksibel. Sistem ini terintegrasi dengan WhatsApp untuk mengirim pesan otomatis setelah pengisian form.</p>

        <h2>2. Arsitektur Sistem</h2>
        <h3>2.1 Model Database</h3>
        <ul>
            <li><strong>Form:</strong> Menyimpan informasi dasar form (nama, deskripsi, status, ID/UUID sebagai primary key)</li>
            <li><strong>FormField:</strong> Menyimpan field-field dalam form (label, type, placeholder, required, options, sort_order)</li>
            <li><strong>Response:</strong> Menyimpan response pengisian form (form_id, store_id, email)</li>
            <li><strong>ResponseAnswer:</strong> Menyimpan jawaban untuk setiap field</li>
            <li><strong>Store:</strong> Menyimpan data toko</li>
            <li><strong>User:</strong> Data pengguna admin</li>
            <li><strong>WhatsappDevice & WhatsappMessage:</strong> Untuk integrasi WhatsApp</li>
        </ul>

        <h3>2.2 Alur Kerja</h3>
        <ol>
            <li>Admin membuat form baru melalui panel admin</li>
            <li>Admin menambahkan field-field ke form (nama, alamat, email, dll)</li>
            <li>Form dipublikasikan dengan status 'active'</li>
            <li>User mengakses form via URL menggunakan nilai primary key (UUID) di segmen terakhir</li>
            <li>User memilih store dan memasukkan email (Step 1)</li>
            <li>User mengisi field-field form dan menyetujui privacy consent (Step 2)</li>
            <li>Data disimpan dan WhatsApp message dikirim otomatis</li>
        </ol>

        <h2>3. Fitur Utama</h2>
        <h3>3.1 Form Builder Dinamis</h3>
        <p>Admin dapat menambahkan/menghapus field secara real-time dalam satu halaman edit form. Field yang didukung:</p>
        <ul>
            <li>Text: Input teks biasa</li>
            <li>Number: Input angka</li>
            <li>Date: Input tanggal</li>
            <li>Email: Input email dengan validasi</li>
            <li>Tel: Input nomor telepon</li>
            <li>Textarea: Text area untuk teks panjang</li>
            <li>Radio: Pilihan radio button</li>
            <li>Select: Dropdown select</li>
        </ul>

        <h3>3.2 Privacy Consent</h3>
        <p>Sebelum submit, user harus menyetujui:</p>
        <ul>
            <li>Pemrosesan data pribadi sesuai GDPR</li>
            <li>Syarat dan ketentuan</li>
            <li>Kebijakan privasi</li>
        </ul>

        <h3>3.3 Validasi Dinamis</h3>
        <p>Validasi field dilakukan berdasarkan konfigurasi:</p>
        <ul>
            <li>Required/optional</li>
            <li>Type validation (email, number)</li>
            <li>Consent validation</li>
        </ul>

        <h3>3.4 Integrasi WhatsApp</h3>
        <p>Setelah form disubmit, sistem otomatis mengirim pesan WhatsApp ke device yang terdaftar menggunakan GOWA integration.</p>

        <h2>4. Cara Penggunaan</h2>
        <h3>4.1 Membuat Form Baru</h3>
        <ol>
            <li>Login ke panel admin</li>
            <li>Klik "Forms" > "Create New Form"</li>
            <li>Masukkan nama dan deskripsi form</li>
            <li>Set status menjadi "Active"</li>
            <li>Klik "Create Form"</li>
        </ol>

        <h3>4.2 Menambah Field ke Form</h3>
        <ol>
            <li>Buka form yang sudah dibuat</li>
            <li>Klik "Edit"</li>
            <li>Di section "Form Fields", klik "Add Field"</li>
            <li>Isi label, pilih type, placeholder, dll</li>
            <li>Untuk radio/select, isi options (satu per baris)</li>
            <li>Klik "Update Form"</li>
        </ol>

        <h3>4.3 Menggunakan Form (User)</h3>
        <ol>
            <li>Akses URL form: <code>/form/{id}</code> (id adalah UUID dari record)</li>
            <li>Pilih store dari dropdown</li>
            <li>Masukkan email</li>
            <li>Klik "Next"</li>
            <li>Isi semua field yang diminta</li>
            <li>Centang semua checkbox consent</li>
            <li>Klik "Submit"</li>
        </ol>

        <h2>5. Teknologi yang Digunakan</h2>
        <ul>
            <li><strong>Backend:</strong> Laravel 10.x</li>
            <li><strong>Database:</strong> MySQL</li>
            <li><strong>Frontend:</strong> Blade templates, Tailwind CSS, JavaScript vanilla</li>
            <li><strong>WhatsApp Integration:</strong> GOWA (Go WhatsApp Web Multi-Device)</li>
            <li><strong>QR Code:</strong> simple-qrcode package</li>
        </ul>

        <h2>6. API Endpoints</h2>
        <h3>6.1 Admin Routes</h3>
        <ul>
            <li><code>GET /admin/forms</code> - List semua forms</li>
            <li><code>GET /admin/forms/create</code> - Form create</li>
            <li><code>POST /admin/forms</code> - Store form baru</li>
            <li><code>GET /admin/forms/{id}/edit</code> - Form edit</li>
            <li><code>PUT /admin/forms/{id}</code> - Update form</li>
            <li><code>DELETE /admin/forms/{id}</code> - Delete form</li>
        </ul>

        <h3>6.2 Public Routes</h3>
        <ul>
            <li><code>GET /form/{id}</code> - Show form (step1/step2)</li>
            <li><code>POST /form/{id}</code> - Submit form</li>
        </ul>

        <h2>7. Keamanan</h2>
        <ul>
            <li>CSRF protection pada semua form</li>
            <li>Validasi input server-side</li>
            <li>Rate limiting pada form submission (3x per email per form)</li>
            <li>Session-based multi-step form</li>
            <li>ID/UUID untuk public form access</li>
        </ul>

        <h2>8. Troubleshooting</h2>
        <h3>8.1 Form tidak bisa disubmit</h3>
        <ul>
            <li>Pastikan semua field required sudah diisi</li>
            <li>Pastikan semua checkbox consent dicentang</li>
            <li>Periksa validasi error message</li>
        </ul>

        <h3>8.2 Field tidak muncul</h3>
        <ul>
            <li>Pastikan form status = 'active'</li>
            <li>Periksa sort_order field</li>
            <li>Clear cache jika perlu</li>
        </ul>

        <h3>8.3 WhatsApp message tidak terkirim</h3>
        <ul>
            <li>Periksa koneksi GOWA</li>
            <li>Periksa device status</li>
            <li>Lihat log error</li>
        </ul>

        <h2>10. Penjelasan Kode Detail</h2>
        <h3>10.1 Model Database</h3>
        <h4>Form Model (app/Models/Form.php)</h4>
        <pre><code>&lt;?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Form extends Model
{
    use HasUuids; // Menggunakan UUID sebagai primary key

    protected $fillable = ['name', 'uuid', 'description', 'status'];

    // Relasi dengan fields
    public function fields()
    {
        return $this->hasMany(FormField::class)->orderBy('sort_order');
    }

    // Relasi dengan responses
    public function responses()
    {
        return $this->hasMany(Response::class);
    }
}
</code></pre>
        <p><strong>Penjelasan:</strong> Model Form menggunakan HasUuids trait sehingga primary key adalah UUID. Relasi hasMany ke FormField dan Response.</p>

        <h4>FormField Model (app/Models/FormField.php)</h4>
        <pre><code>&lt;?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormField extends Model
{
    protected $fillable = ['form_id', 'label', 'type', 'placeholder', 'required', 'options', 'sort_order'];

    protected $casts = [
        'options' => 'array', // Otomatis cast ke array
        'required' => 'boolean',
    ];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function answers()
    {
        return $this->hasMany(ResponseAnswer::class, 'field_id');
    }
}
</code></pre>
        <p><strong>Penjelasan:</strong> Model FormField dengan cast options ke array untuk menyimpan pilihan radio/select.</p>

        <h3>10.2 Controller Logic</h3>
        <h4>FormController Update Method</h4>
        <pre><code>public function update(Request $request, string $id)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'status' => 'required|in:active,inactive',
        'fields' => 'array',
        'fields.*.id' => 'nullable|exists:form_fields,id',
        'fields.*.label' => 'required|string|max:255',
        'fields.*.type' => 'required|in:text,number,date,radio,select,textarea,email,tel',
        'fields.*.placeholder' => 'nullable|string',
        'fields.*.required' => 'boolean',
        'fields.*.options' => 'nullable|array',
        'fields.*.sort_order' => 'integer',
    ]);

    $form = Form::findOrFail($id);
    $form->update($request->only(['name', 'description', 'status']));

    // Handle fields
    $existingFieldIds = $form->fields->pluck('id')->toArray();
    $updatedFieldIds = [];

    if ($request->has('fields')) {
        foreach ($request->fields as $index => $fieldData) {
            if (isset($fieldData['id']) && $fieldData['id']) {
                // Update existing field
                $field = FormField::find($fieldData['id']);
                if ($field && $field->form_id == $form->id) {
                    $field->update([
                        'label' => $fieldData['label'],
                        'type' => $fieldData['type'],
                        'placeholder' => $fieldData['placeholder'] ?? null,
                        'required' => $fieldData['required'] ?? false,
                        'options' => isset($fieldData['options']) ? json_encode($fieldData['options']) : null,
                        'sort_order' => $fieldData['sort_order'] ?? $index + 1,
                    ]);
                    $updatedFieldIds[] = $field->id;
                }
            } else {
                // Create new field
                $field = FormField::create([
                    'form_id' => $form->id,
                    'label' => $fieldData['label'],
                    'type' => $fieldData['type'],
                    'placeholder' => $fieldData['placeholder'] ?? null,
                    'required' => $fieldData['required'] ?? false,
                    'options' => isset($fieldData['options']) ? json_encode($fieldData['options']) : null,
                    'sort_order' => $fieldData['sort_order'] ?? $index + 1,
                ]);
                $updatedFieldIds[] = $field->id;
            }
        }
    }

    // Delete fields that are no longer present
    $fieldsToDelete = array_diff($existingFieldIds, $updatedFieldIds);
    FormField::whereIn('id', $fieldsToDelete)->delete();

    return redirect()->route('admin.forms.index')->with('success', 'Form updated successfully.');
}
</code></pre>
        <p><strong>Penjelasan:</strong> Method ini menangani update form dan fields secara bersamaan. Melakukan validasi array fields, update/create/delete fields berdasarkan perubahan.</p>

        <h4>FormSubmitController Store Method</h4>
        <pre><code>public function store(Request $request, Form $form)
{
    if ($form->status !== 'active') {
        abort(404);
    }

    if (!$request->session()->has('form_data')) {
        // Step 1 validation
        $request->validate([
            'email' => 'required|email',
            'store_id' => 'required|exists:stores,id',
        ]);

        // Check submission limit (3 per campaign per email)
        $existingCount = Response::where('form_id', $form->id)
            ->where('email', $request->email)
            ->count();

        if ($existingCount >= 3) {
            return back()->withErrors(['email' => 'You have already submitted this form 3 times.']);
        }

        // Store step 1 data in session
        $request->session()->put('form_data', [
            'email' => $request->email,
            'store_id' => $request->store_id,
        ]);
        $request->session()->put('form_step', 2);

        return redirect()->route('public.form.show', $form->id);
    } else {
        // Step 2: process form submission
        $formData = $request->session()->get('form_data');
        $form->load('fields');

        // Validate dynamic fields and consents
        $rules = [
            'consent_personal_data' => 'required|accepted',
            'consent_terms' => 'required|accepted',
            'consent_privacy_policy' => 'required|accepted',
        ];
        foreach ($form->fields as $field) {
            $rule = $field->required ? 'required' : 'nullable';
            if ($field->type == 'email') {
                $rule .= '|email';
            }
            $rules["field_{$field->id}"] = $rule;
        }
        $request->validate($rules);

        // Create response
        $response = Response::create([
            'form_id' => $form->id,
            'store_id' => $formData['store_id'],
            'email' => $formData['email'],
        ]);

        // Save answers
        foreach ($form->fields as $field) {
            $value = $request->input("field_{$field->id}");
            if ($value !== null) {
                ResponseAnswer::create([
                    'response_id' => $response->id,
                    'field_id' => $field->id,
                    'value' => $value,
                ]);
            }
        }

        // Send WhatsApp message
        $this->sendWhatsAppMessage($response);

        // Clear session
        $request->session()->forget(['form_data', 'form_step']);

        return view('public.form.success');
    }
}
</code></pre>
        <p><strong>Penjelasan:</strong> Controller ini menangani 2-step form submission. Step 1: pilih store & email. Step 2: isi form & consent. Validasi dinamis berdasarkan field configuration.</p>

        <h3>10.3 Frontend JavaScript (Dynamic Field Management)</h3>
        <pre><code>document.addEventListener('DOMContentLoaded', function() {
    let fieldIndex = {{ $form->fields->count() }};

    document.getElementById('add-field').addEventListener('click', function() {
        addField();
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-field')) {
            e.target.closest('.field-item').remove();
            updateFieldIndices();
        }
    });

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('field-type')) {
            const container = e.target.closest('.field-item');
            const optionsContainer = container.querySelector('.options-container');
            if (['radio', 'select'].includes(e.target.value)) {
                optionsContainer.style.display = 'block';
            } else {
                optionsContainer.style.display = 'none';
            }
        }
    });

    document.addEventListener('input', function(e) {
        if (e.target.name && e.target.name.endsWith('[options_text]')) {
            const container = e.target.closest('.field-item');
            const hiddenInput = container.querySelector('input[name*="[options]"]');
            const options = e.target.value.split('\n').map(opt => opt.trim()).filter(opt => opt);
            hiddenInput.value = JSON.stringify(options);
        }
    });

    function addField() {
        const container = document.getElementById('fields-container');
        const fieldHtml = `
            // HTML template for new field
        `;
        container.insertAdjacentHTML('beforeend', fieldHtml);
        fieldIndex++;
    }

    function updateFieldIndices() {
        const fieldItems = document.querySelectorAll('.field-item');
        fieldItems.forEach((item, index) => {
            item.setAttribute('data-index', index);
            const inputs = item.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                if (input.name) {
                    input.name = input.name.replace(/fields\[\d+\]/, `fields[${index}]`);
                }
            });
        });
    }
});
</code></pre>
        <p><strong>Penjelasan:</strong> JavaScript untuk menambah/hapus field secara dinamis. Mengupdate index array dan menampilkan options container untuk radio/select fields.</p>

        <h3>10.4 Migration Structure</h3>
        <pre><code>// Forms table
Schema::create('forms', function (Blueprint $table) {
    $table->uuid('id')->primary(); // UUID primary key
    $table->string('name');
    $table->text('description')->nullable();
    $table->enum('status', ['active', 'inactive'])->default('active');
    $table->timestamps();
});

// Form fields table
Schema::create('form_fields', function (Blueprint $table) {
    $table->id();
    $table->uuid('form_id');
    $table->foreign('form_id')->references('id')->on('forms')->onDelete('cascade');
    $table->string('label');
    $table->enum('type', ['text', 'number', 'date', 'radio', 'select', 'textarea', 'email', 'tel']);
    $table->string('placeholder')->nullable();
    $table->boolean('required')->default(false);
    $table->json('options')->nullable(); // for radio/select options
    $table->integer('sort_order')->default(0);
    $table->timestamps();
});
</code></pre>
        <p><strong>Penjelasan:</strong> Struktur database dengan UUID untuk forms dan foreign key relationships.</p>

        <h2>11. Troubleshooting & Fixes</h2>
        <h3>11.1 Maximum Execution Time Timeout saat Link Device</h3>
        <p><strong>Penyebab:</strong> HTTP request ke GoWA API tidak memiliki timeout, menyebabkan infinite wait jika server tidak merespons.</p>
        <p><strong>Solusi:</strong> Menambahkan timeout 30 detik pada HTTP request di GowaService::getDevices().</p>
        <pre><code>$response = Http::timeout(30)->get("{$this->baseUrl}/devices");</code></pre>

        <h3>11.2 Error "Missing required parameter" untuk Route QR</h3>
        <p><strong>Penyebab:</strong> Struktur tabel menggunakan kolom <code>id</code> sebagai UUID primary key. Sebelumnya kode mencoba mengakses properti <code>$form-&gt;uuid</code> dan menetapkan <code>getRouteKeyName()</code> ke 'uuid' — tetapi kolom tersebut tidak ada. Akibatnya Laravel tidak dapat membangun URL karena nilainya selalu <code>null</code>, memicu <code>UrlGenerationException</code>.</p>
        <p><strong>Solusi:</strong> Gunakan model itu sendiri (atau <code>$form-&gt;id</code>) saat memanggil <code>route(...)</code> dan biarkan binding model Laravel bekerja dengan primary key. Jangan override <code>getRouteKeyName()</code> kecuali Anda benar-benar memiliki kolom lain. Perbarui juga blade untuk tidak mengandalkan <code>$form-&gt;uuid</code>.</p>

        <h3>11.3 Auto-update Nomor Telepon Store</h3>
        <p><strong>Fitur:</strong> Saat device WhatsApp di-sync, nomor telepon dari device yang logged in akan otomatis mengupdate store yang terhubung.</p>
        <pre><code>// Di GowaService::getDevices()
if ($device->is_logged_in && $device->phone_number) {
    $store = \App\Models\Store::where('whatsapp_device_id', $deviceId)->first();
    if ($store && (!$store->phone_number || $store->phone_number !== $device->phone_number)) {
        $store->update(['phone_number' => $device->phone_number]);
    }
}</code></pre>

        <h3>11.4 Email Notification dengan PHPMailer</h3>
        <p><strong>Fitur:</strong> Sistem email notification yang dapat diaktifkan/nonaktifkan per form dengan template yang dapat dikustomisasi.</p>
        <p><strong>Konfigurasi:</strong></p>
        <ul>
            <li>Menambahkan kolom email settings ke forms table</li>
            <li>Membuat EmailService menggunakan PHPMailer</li>
            <li>Template variables: {form_name}, {email}, {store_name}, {admin_url}, {submission_data}</li>
        </ul>

        <h2>12. Testing Guide</h2>
        <h3>12.1 Setup Testing Environment</h3>
        <ol>
            <li>Jalankan migration dan seeder:
                <pre><code>php artisan migrate:fresh --seed</code></pre>
            </li>
            <li>Start Laravel server:
                <pre><code>php artisan serve --host=127.0.0.1 --port=8000</code></pre>
            </li>
            <li>Setup GoWA Docker (opsional untuk WhatsApp testing)</li>
        </ol>

        <h3>12.2 Test Form Submission</h3>
        <ol>
            <li>Buka form: <code>http://127.0.0.1:8000/form/{id}</code> (UUID primary key)</li>
            <li>Pilih store dari dropdown</li>
            <li>Masukkan email</li>
            <li>Isi semua field yang required</li>
            <li>Centang semua consent checkboxes</li>
            <li>Submit form</li>
            <li>Verifikasi data tersimpan di database</li>
        </ol>

        <h3>12.3 Test Device Linking</h3>
        <ol>
            <li>Login ke admin panel</li>
            <li>Buat device baru di menu Devices</li>
            <li>Sync devices dari GoWA</li>
            <li>Link device ke store</li>
            <li>Verifikasi phone number terupdate otomatis</li>
        </ol>

        <h3>12.4 Test Email Notification</h3>
        <ol>
            <li>Edit form dan aktifkan email notification</li>
            <li>Setup email credentials di .env</li>
            <li>Submit form</li>
            <li>Verifikasi email terkirim ke admin</li>
        </ol>

        <h2>13. Pengembangan Lanjutan</h2>
        <ul>
            <li>Integrasi dengan Google Forms API</li>
            <li>Export data ke Excel/CSV</li>
            <li>Dashboard analytics</li>
            <li>Multi-language support</li>
            <li>Conditional fields (show/hide based on answers)</li>
        </ul>
    </div>
</div>
@endsection