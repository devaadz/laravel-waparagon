<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\Store;
use App\Models\FormStore;
use App\Models\Response;
use App\Models\ResponseAnswer;
use Illuminate\Http\Request;
use App\Services\EmailService;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class FormSubmitController extends Controller
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function show(Request $request, Form $form)
    {
        if ($form->status !== 'active') {
            abort(404);
        }
        $stores = Store::all();

        if ($request->session()->has('form_step') && $request->session()->get('form_step') == 2) {
            $form->load('fields');
            return view('public.form.step2', compact('form'));
        } else {
            return view('public.form.step1', compact('form', 'stores'));
        }
    }

    public function store(Request $request, Form $form)
    {
        // ... (existing store method unchanged)
    }

    public function qr(Form $form)
    {
        if ($form->status !== 'active') {
            abort(404);
        }
        $url = route('public.form.show', $form->id);
        return response(QrCode::size(300)->generate($url))->header('Content-Type', 'image/png');
    }

    public function preview(Form $form)
    {
        if ($form->status !== 'active') {
            abort(404);
        }
        $form->load('fields');
        $stores = Store::all();
        return view('public.form.preview', compact('form', 'stores'));
    }

    /**
     * Show form by store-specific URL slug.
     */
    public function showBySlug(Request $request, string $slug)
    {
        $formStore = FormStore::where('custom_url_slug', $slug)->firstOrFail();
        
        $form = $formStore->form;
        if ($form->status !== 'active') {
            abort(404);
        }

        $store = $formStore->store;
        $form->load('fields');

        return view('public.form.step2', compact('form', 'store', 'formStore'));
    }

    /**
     * Store form submission by store-specific URL slug.
     */
    public function storeBySlug(Request $request, string $slug)
    {
        $formStore = FormStore::where('custom_url_slug', $slug)->firstOrFail();
        
        $form = $formStore->form;
        if ($form->status !== 'active') {
            abort(404);
        }

        $store = $formStore->store;
        $form->load('fields');

        $rules = [
            'consent_personal_data' => 'required|accepted',
            'consent_terms' => 'required|accepted',
            'consent_privacy_policy' => 'required|accepted',
            'email' => 'required|email',
        ];
        
        foreach ($form->fields as $field) {
            $rule = $field->required ? 'required' : 'nullable';
            if ($field->type == 'email') {
                $rule .= '|email';
            }
            $rules["field_{$field->id}"] = $rule;
        }
        
        $request->validate($rules);

        // $existingCount = Response::where('form_id', $form->id)
        //     ->where('email', $request->email)
        //     ->count();

        // if ($existingCount >= 3) {
        //     return back()->withErrors(['email' => 'You have already submitted this form 3 times.']);
        // }

        $minutes = 2; // bisa lo custom
        $limit = 3;   // max submit dalam waktu itu

        $timeLimit = Carbon::now()->subMinutes($minutes);

        $count = Response::where('form_id', $form->id)
            ->where('email', $request->email)
            ->where('created_at', '>=', $timeLimit)
            ->count();

        if ($count >= $limit) {
            return back()->withErrors([
                'email' => "Terlalu banyak submit. Maksimal {$limit}x dalam {$minutes} menit."
            ]);
        }

        $response = Response::create([
            'form_id' => $form->id,
            'store_id' => $store->id,
            'email' => $request->email,
        ]);

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

        // $this->sendWhatsAppMessage($response);
        if ($form->enable_email_notification && $form->email_subject && $form->email_template) {
            $this->sendEmailNotification($response);
        }
        if ($form->enable_whatsapp_notification == '1') {
            $this->sendWhatsAppMessage($response);
        }
        
        if ($form->enable_whatsapp_image == '1') {
            $this->sendWhatsAppMessageWithImage($response);
        }
        return view('public.form.success');
    }

    public function qrBySlug(string $slug)
    {
        $formStore = FormStore::where('custom_url_slug', $slug)->firstOrFail();
        $url = '/form/' . $slug;

        return response(QrCode::size(300)->generate($url))->header('Content-Type', 'image/png');
    }

    // Keep all private methods unchanged (sendWhatsAppMessage, etc.)
    private function sendWhatsAppMessage(Response $response)
    {
        try {
            // 🔥 Load relasi sekalian (biar hemat query)
            $response->load(['store.whatsappDevice', 'form']);
            // dd($response->store, $response->store->whatsappDevice);
            // ❌ kalau store atau device gak ada → stop
            if (!$response->store || !$response->store->whatsappDevice) {
                Log::warning('WA not sent: device not found', [
                    'response_id' => $response->id
                ]);
                // dd("masukl ke if error");
                return;
            }
            
            $deviceId = $response->store->whatsappDevice->device_id;
            
            // 🔍 Ambil semua jawaban
            $answers = ResponseAnswer::with('field')
                ->where('response_id', $response->id)
                ->get();
            // dd("keluar if bawah answers ", $answers, $deviceId);
            // dd($answers,$deviceId, $answers->field);
            // 🔍 Cari nomor
            $phoneAnswer = ResponseAnswer::where('response_id', $response->id)
                ->whereHas('field', function ($q) use ($response) {
                    $q->where('form_id', $response->form_id)
                    ->where(function ($q2) {
                        $q2->where('type', 'phone')
                            ->orWhereRaw('LOWER(label) LIKE ?', ['%phone%'])
                            ->orWhereRaw('LOWER(label) LIKE ?', ['%hp%'])
                            ->orWhereRaw('LOWER(label) LIKE ?', ['%wa%'])
                            ->orWhereRaw('LOWER(label) LIKE ?', ['%whatsapp%'])
                            ->orWhereRaw('LOWER(label) LIKE ?', ['%nomor%'])
                            ->orWhereRaw('LOWER(label) LIKE ?', ['%telepon%']);
                    });
                })
                ->first();
            if (!$phoneAnswer || empty($phoneAnswer->value)) {
                Log::warning('WA not sent: phone not found', [
                    'response_id' => $response->id
                ]);
                return;
            }

            // 📱 format nomor
            $formattedPhone = $this->formatPhone($phoneAnswer->value);

            // 📝 template dari DB
            $message = $response->form->whatsapp_template 
                ?? "Halo 👋\nTerima kasih sudah mengisi form 🙌";
                // 🚀 HIT API
                // buat if kalo msial 1 maka masuk kalo 0 makan biarin tambhaklakn untuk gamabr 
                if ($response->form->enable_whatsapp_notification == '1')
                    {
                        $res = Http::timeout(10)
                            ->withHeaders([
                                'X-Device-Id' => $deviceId
                            ])
                            ->post(env('GOWA_URL') . '/send/message', [
                                'phone' => $formattedPhone,
                                'message' => $message
                            ]);
            
                        if ($res->successful()) {
                            Log::info('WA sent', [
                                'response_id' => $response->id,
                                'device_id' => $deviceId,
                                'phone' => $formattedPhone,
                                'pesan' => $message
                            ]);
                        } else {
                            Log::error('WA failed', [
                                'response_id' => $response->id,
                                'device_id' => $deviceId,
                                'status' => $res->status(),
                                'body' => $res->body()
                            ]);
                        }
                    }
                // if ($response->form->enable_whatsapp_image == '1')
                //     {
                //         $relativePath = $response->form->whatsapp_image; // dari DB

                //         $path = storage_path('app/public/' . $relativePath);

                //         if (!file_exists($path)) {
                //             dd('File tidak ditemukan: ' . $path);
                //         }

                //         $mime = mime_content_type($path);
                //         $name = basename($path);

                //         $res = Http::timeout(10)
                //             ->withHeaders([
                //                 'X-Device-Id' => $deviceId
                //             ])
                //             ->attach('image', file_get_contents($path), $name, [
                //                 'Content-Type' => $mime
                //             ])
                //             ->post(env('GOWA_URL') . '/send/image', [
                //                 'phone' => $formattedPhone,
                //                 'caption' => 'Ini gambar dari sistem'
                //             ]);
                //         if ($res->successful()) {
                //             Log::info('WA sent', [
                //                 'response_id' => $response->id,
                //                 'device_id' => $deviceId,
                //                 'phone' => $formattedPhone,
                //                 'pesan' => 'gambar ke send'
                //             ]);
                //         } else {
                //             Log::error('WA failed', [
                //                 'response_id' => $response->id,
                //                 'device_id' => $deviceId,
                //                 'status' => $res->status(),
                //                 'body' => $res->body()
                //             ]);
                //         }
                //     }

        } catch (\Throwable $e) {
            Log::error('WA error', [
                'response_id' => $response->id,
                'error' => $e->getMessage()
            ]);
        }
    }
    private function sendEmailNotification(Response $response) { /* ... */ }
    private function sendWhatsAppNotification(Response $response)
    {
        $this->sendWhatsAppMessageWithImage($response);
    }

    private function sendWhatsAppMessageWithImage(Response $response)
    {
        try {
            $response->load(['store.whatsappDevice', 'form']);
            if (!$response->store || !$response->store->whatsappDevice) {
                Log::warning('WA not sent: device not found', ['response_id' => $response->id]);
                return;
            }
            
            $deviceId = $response->store->whatsappDevice->device_id;
            
            $answers = ResponseAnswer::with('field')->where('response_id', $response->id)->get();
            
            $phoneAnswer = ResponseAnswer::where('response_id', $response->id)
                ->whereHas('field', function ($q) use ($response) {
                    $q->where('form_id', $response->form_id)
                    ->where(function ($q2) {
                        $q2->where('type', 'phone')
                            ->orWhereRaw('LOWER(label) LIKE ?', ['%phone%'])
                            ->orWhereRaw('LOWER(label) LIKE ?', ['%hp%'])
                            ->orWhereRaw('LOWER(label) LIKE ?', ['%wa%'])
                            ->orWhereRaw('LOWER(label) LIKE ?', ['%whatsapp%'])
                            ->orWhereRaw('LOWER(label) LIKE ?', ['%nomor%'])
                            ->orWhereRaw('LOWER(label) LIKE ?', ['%telepon%']);
                    });
                })
                ->first();
                
            if (!$phoneAnswer || empty($phoneAnswer->value)) {
                Log::warning('WA not sent: phone not found', ['response_id' => $response->id]);
                return;
            }

            $formattedPhone = $this->formatPhone($phoneAnswer->value);
            $message = $response->form->whatsapp_template ?? "New form submission received.";

            $imageData = null;
            if ($response->form->enable_whatsapp_image && $response->form->whatsapp_image) {
                $imagePath = storage_path('app/public/' . $response->form->whatsapp_image);
                if (file_exists($imagePath)) {
                    $imageData = [
                        'image' => $imagePath,
                        'caption' => $message
                    ];
                    // Send image
                    $res = Http::timeout(30)
                        ->withHeaders(['X-Device-Id' => $deviceId])
                        ->attach('image', file_get_contents($imagePath), basename($imagePath))
                        ->post(env('GOWA_URL') . '/send/image', [
                            'phone' => $formattedPhone,
                            'caption' => $message
                        ]);
                } else {
                    // Fallback to text
                    // $res = Http::timeout(10)
                    //     ->withHeaders(['X-Device-Id' => $deviceId])
                    //     ->post(env('GOWA_URL') . '/send/message', [
                    //         'phone' => $formattedPhone,
                    //         'message' => $message
                    //     ]);
                }
            } else {
                // Text only
                // $res = Http::timeout(10)
                //     ->withHeaders(['X-Device-Id' => $deviceId])
                //     ->post(env('GOWA_URL') . '/send/message', [
                //         'phone' => $formattedPhone,
                //         'message' => $message
                //     ]);
            }

            if ($res->successful()) {
                Log::info('WA sent', [
                    'response_id' => $response->id,
                    'device_id' => $deviceId,
                    'phone' => $formattedPhone,
                    'has_image' => !empty($imageData)
                ]);
            } else {
                Log::error('WA failed', [
                    'response_id' => $response->id,
                    'status' => $res->status(),
                    'body' => $res->body()
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('WA error', [
                'response_id' => $response->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function formatPhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // 08 → 628
        if (substr($phone, 0, 1) == '0') {
            $phone = '62' . substr($phone, 1);
        }

        // kalau sudah 62 biarin
        if (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        return $phone . '@s.whatsapp.net';
    }
}

