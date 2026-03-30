<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\Store;
use App\Models\FormStore;
use App\Models\Response;
use App\Models\ResponseAnswer;
use App\Models\NotificationLog;
use Illuminate\Http\Request;
use App\Services\EmailService;
use App\Services\GowaService;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class FormSubmitController extends Controller
{
    protected $emailService;
    protected $gowaService;

    public function __construct(EmailService $emailService, GowaService $gowaService)
    {
        $this->emailService = $emailService;
        $this->gowaService = $gowaService;
    }

    public function show(Request $request, Form $form)
    {
        if ($form->status !== 'active') {
            abort(404);
        }
        $stores = Store::all();

        if ($request->session()->has('form_step') && $request->session()->get('form_step') == 2) {
            $form->load('fields');

            $store = null;
            if ($request->session()->has('form_data.store_id')) {
                $store = Store::find($request->session()->get('form_data.store_id'));
            }

            return view('public.form.step2', compact('form', 'store'));
        } else {
            return view('public.form.step1', compact('form', 'stores'));
        }
    }

    public function store(Request $request, Form $form)
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
                    // Handle array values (like checkboxes)
                    if (is_array($value)) {
                        $value = json_encode($value);
                    }
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
        
        // Add WhatsApp phone validation if enabled
        if ($form->enable_whatsapp_notification) {
            $rules['whatsapp_phone'] = 'required|regex:/^[0-9]{9,12}$/|not_regex:/^0/';
        }
        
        foreach ($form->fields as $field) {
            $rule = $field->required ? 'required' : 'nullable';
            if ($field->type == 'email') {
                $rule .= '|email';
            }
            $rules["field_{$field->id}"] = $rule;
        }
        
        $request->validate($rules);


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
            'whatsapp_phone' => $form->enable_whatsapp_notification ? $request->whatsapp_phone : null,
        ]);

        foreach ($form->fields as $field) {
            $value = $request->input("field_{$field->id}");
            if ($value !== null) {
                // Handle array values (like checkboxes)
                if (is_array($value)) {
                    $value = json_encode($value);
                }
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
        
        // Handle WhatsApp notifications
        if ($form->enable_whatsapp_notification == '1') {
            // If image is enabled and template should be used as caption, only send image
            if ($form->enable_whatsapp_image == '1' && $form->whatsapp_template_as_caption) {
                $this->sendWhatsAppMessageWithImage($response);
            } else {
                // Send text message
                $this->sendWhatsAppMessage($response);
                
                // Send image separately if enabled
                if ($form->enable_whatsapp_image == '1') {
                    $this->sendWhatsAppMessageWithImage($response);
                }
            }
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
            // 📱 Prioritas: gunakan whatsapp_phone dari response dulu (inputan user)
            $phoneNumber = $response->whatsapp_phone;
            
            // Jika tidak ada, cari dari field
            if (!$phoneNumber) {
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
                
                if ($phoneAnswer) {
                    $phoneNumber = $phoneAnswer->value;
                }
            }
            
            // Validasi nomor ada atau tidak
            if (!$phoneNumber || empty($phoneNumber)) {
                Log::warning('WA not sent: phone not found', [
                    'response_id' => $response->id
                ]);
                return;
            }

            // 📱 format nomor
            $formattedPhone = $this->formatPhone($phoneNumber);

            // 📝 template dari DB dengan placeholder replacement
            $template = $response->form->whatsapp_template 
                ?? "Halo, Terima kasih sudah mengisi form kami.";
            $message = $this->replaceTemplatePlaceholders($template, $response);
            // dd("masuk ke function sendWhatsAppMessage",$template, $response, $formattedPhone, $message);
            // 🚀 Log notification (sebelum kirim)
            $notifLog = NotificationLog::logNotification(
                $response->form_id,
                $response->id,
                'whatsapp',
                $formattedPhone,
                $message,
                'pending',
                null, // error_message
                $response->store->whatsappDevice->id ?? null, // whatsapp_device_id
                $response->store->whatsappDevice->name ?? null, // device_name
                $response->store->whatsappDevice->system ?? null // device_system
            );
            
            // Kirim ke GOWA
                try{
                    if ($response->form->enable_whatsapp_notification == '1') {
                        $messageResult = $this->gowaService->sendMessage(
                            $formattedPhone,
                            $message,
                            $deviceId
                        );
                
                        if ($messageResult['success']) {
                            // Mark as sent
                            $notifLog->update([
                                'status' => 'sent',
                                'sent_at' => now(),
                            ]);
                            
                            Log::info('WA sent', [
                                'response_id' => $response->id,
                                'device_id' => $deviceId,
                                'phone' => $formattedPhone,
                            ]);
                        } else {
                            // Mark as failed
                            $errMsg = $messageResult['error'] ?? $messageResult['message'];
                            $notifLog->update([
                                'status' => 'failed',
                                'error_message' => $errMsg,
                            ]);
                                
                            Log::error('WA failed', [
                                'response_id' => $response->id,
                                'device_id' => $deviceId,
                                'body' => $errMsg
                            ]);
                            }
                        } 
                }catch (\Exception $e) {
                    // Mark as failed with exception
                    $notifLog->update([
                        'status' => 'failed',
                        'error_message' => $e->getMessage(),
                    ]);
                    
                    Log::error('WA exception', [
                        'response_id' => $response->id,
                        'error' => $e->getMessage()
                    ]);
                }
            
        } catch (\Throwable $e) {
            Log::error('WA error', [
                'response_id' => $response->id,
                'error' => $e->getMessage()
            ]);
        }
    }
    private function sendEmailNotification(Response $response) 
    { /* ... */ }
    

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
                // dd("masuk ke function sendWhatsAppMessageWithImage", $response);
                $phoneNumber = $response->whatsapp_phone;
            
                // Jika tidak ada, cari dari field
                if (!$phoneNumber) {
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
                    
                    if ($phoneAnswer) {
                        $phoneNumber = $phoneAnswer->value;
                    }
                }
                
                // Validasi nomor ada atau tidak
                if (!$phoneNumber || empty($phoneNumber)) {
                    Log::warning('WA not sent: phone not found', [
                        'response_id' => $response->id
                    ]);
                    return;
                }
                
            // 📱 format nomor
            $formattedPhone = $this->formatPhone($phoneNumber);

            // 📝 template dari DB dengan placeholder replacement
            $template = $response->form->whatsapp_template ?? "New form submission received.";
            $message = $this->replaceTemplatePlaceholders($template, $response);

            $imageData = null;
            if ($response->form->enable_whatsapp_image && $response->form->whatsapp_image) {
                $imagePath = storage_path('app/public/' . $response->form->whatsapp_image);
                if (file_exists($imagePath)) {
                    // Determine caption based on settings
                    $caption = '';
                    if ($response->form->whatsapp_template_as_caption) {
                        $caption = $message;
                    }

                    // Send image with or without caption
                    $imageResult = $this->gowaService->sendImage(
                        $formattedPhone,
                        $imagePath,
                        $caption,
                        $deviceId
                    );

                    // Log notification for image
                    $notifLog = NotificationLog::logNotification(
                        $response->form_id,
                        $response->id,
                        'whatsapp_image',
                        $formattedPhone,
                        $caption ?: 'Image sent',
                        'pending',
                        null, // error_message
                        $response->store->whatsappDevice->id ?? null, // whatsapp_device_id
                        $response->store->whatsappDevice->name ?? null, // device_name
                        $response->store->whatsappDevice->system ?? null // device_system
                    );

                    if ($imageResult['success']) {
                        $notifLog->update([
                            'status' => 'sent',
                            'sent_at' => now(),
                        ]);
                        Log::info('WA image sent', [
                            'response_id' => $response->id,
                            'device_id' => $deviceId,
                            'phone' => $formattedPhone,
                            'has_caption' => !empty($caption)
                        ]);
                    } else {
                        $errMsg = $imageResult['error'] ?? $imageResult['message'];
                        $notifLog->update([
                            'status' => 'failed',
                            'error_message' => $errMsg,
                        ]);
                        Log::error('WA image failed', [
                            'response_id' => $response->id,
                            'error' => $errMsg,
                            'status' => $response->status(),
                            'body' => $response->body()
                        ]);
                    }
                }
            }

        } catch (\Throwable $e) {
            Log::error('WhatsApp message sending failed', [
                'response_id' => $response->id,
                'error' => $e->getMessage()
            ]);
        }
        // dd("keluar function sendWhatsAppMessageWithImage");
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

    /**
     * Replace placeholders in WhatsApp template
     */
    private function replaceTemplatePlaceholders(string $template, Response $response): string
    {
        $response->load(['form', 'store', 'answers.field']);

        // 1. Store name fallback from response store
        $storeName = $response->store->name ?? '';

        // 2. Customer name detection from form fields/answers
        $customerName = '';
        foreach ($response->answers as $answer) {
            $label = strtolower(trim($answer->field->label ?? ''));

            if (str_contains($label, 'nama customer')
                || str_contains($label, 'customer name')
                || (str_contains($label, 'nama') && str_contains($label, 'customer'))
                || (str_contains($label, 'name') && str_contains($label, 'customer'))
            ) {
                $customerName = $answer->value;
                break;
            }
        }

        // 3. Default if customer name missing: try any name-like field that is not store-name
        if (!$customerName) {
            foreach ($response->answers as $answer) {
                $label = strtolower(trim($answer->field->label ?? ''));
                $isStoreLabel = str_contains($label, 'toko') || str_contains($label, 'store');
                $isNameLabel = str_contains($label, 'nama') || str_contains($label, 'name');

                if ($isNameLabel && !$isStoreLabel) {
                    $customerName = $answer->value;
                    break;
                }
            }
        }

        $replacements = [
            '{form_name}' => $response->form->name ?? '',
            '(form_name)' => $response->form->name ?? '',
            '{store_name}' => $storeName,
            '(store_name)' => $storeName,
            '{customer_name}' => $customerName,
            '(customer_name)' => $customerName,
            '{email}' => $response->email ?? '',
            '(email)' => $response->email ?? '',
            '{submission_data}' => $this->getSubmissionDataText($response),
            '(submission_data)' => $this->getSubmissionDataText($response),
        ];
        // dd("masuk ke function replaceTemplatePlaceholders", $template, $replacements, $customerName);

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    /**
     * Get formatted submission data for template
     */
    private function getSubmissionDataText(Response $response): string
    {
        $response->load(['answers.field']);
        $data = [];

        foreach ($response->answers as $answer) {
            $data[] = $answer->field->label . ': ' . $answer->value;
        }

        return implode("\n", $data);
    }
}

