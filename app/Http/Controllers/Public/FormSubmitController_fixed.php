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
        $store = null;
        $storeLink = null;

        // Check if form is linked to a store
        if ($request->has('store_id')) {
            $storeLink = FormStore::with('store')->where('form_id', $form->id)
                ->where('store_id', $request->store_id)
                ->first();

            if ($storeLink) {
                $store = $storeLink->store;
            }
        }

        // If no store link found, try to find any store link for this form
        if (!$storeLink) {
            $storeLink = FormStore::with('store')->where('form_id', $form->id)->first();
            if ($storeLink) {
                $store = $storeLink->store;
            }
        }

        return view('public.form.step1', compact('form', 'store'));
    }

    public function store(Request $request, Form $form)
    {
        // Validate the request
        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|string',
        ]);

        // Create response
        $response = Response::create([
            'form_id' => $form->id,
            'store_id' => $request->store_id ?? null,
            'whatsapp_phone' => $request->whatsapp_phone ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Save answers
        foreach ($request->answers as $fieldId => $answer) {
            ResponseAnswer::create([
                'response_id' => $response->id,
                'field_id' => $fieldId,
                'value' => $answer,
            ]);
        }

        // Send notifications
        $this->sendNotifications($response);

        return redirect()->route('form.submitted', $response->id);
    }

    private function sendNotifications(Response $response)
    {
        try {
            $response->load(['store.whatsappDevice', 'form']);

            if ($response->form->enable_email_notification) {
                $this->sendEmailNotification($response);
            }

            if ($response->form->enable_whatsapp_notification) {
                $this->sendWhatsAppMessage($response);
            }

            if ($response->form->enable_whatsapp_image) {
                $this->sendWhatsAppMessageWithImage($response);
            }
        } catch (\Throwable $e) {
            Log::error('Error sending notifications', [
                'response_id' => $response->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function sendEmailNotification(Response $response)
    {
        // Email notification logic here
    }

    private function sendWhatsAppMessage(Response $response)
    {
        try {
            $response->load(['store.whatsappDevice', 'form']);
            if (!$response->store || !$response->store->whatsappDevice) {
                Log::warning('WA not sent: device not found', ['response_id' => $response->id]);
                return;
            }

            $deviceId = $response->store->whatsappDevice->device_id;

            $answers = ResponseAnswer::with('field')->where('response_id', $response->id)->get();
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

            // Format nomor
            $formattedPhone = $this->formatPhone($phoneNumber);

            // Template dari DB dengan placeholder replacement
            $template = $response->form->whatsapp_template ?? "New form submission received.";
            $message = $this->replaceTemplatePlaceholders($template, $response);

            // Send message
            $messageResult = $this->gowaService->sendMessage(
                $formattedPhone,
                $message,
                $deviceId
            );

            // Log notification
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

            if ($messageResult['success']) {
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
                $errMsg = $messageResult['error'] ?? $messageResult['message'];
                $notifLog->update([
                    'status' => 'failed',
                    'error_message' => $errMsg,
                ]);

                Log::error('WA failed', [
                    'response_id' => $response->id,
                    'error' => $errMsg
                ]);
            }
        } catch (\Exception $e) {
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

            // Format nomor
            $formattedPhone = $this->formatPhone($phoneNumber);

            // Template dari DB dengan placeholder replacement
            $template = $response->form->whatsapp_template ?? "New form submission received.";
            $message = $this->replaceTemplatePlaceholders($template, $response);

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
                            'error' => $errMsg
                        ]);
                    }
                }
            }
        } catch (\Throwable $th) {
            Log::error('Error sending WhatsApp message with image', [
                'response_id' => $response->id,
                'error' => $th->getMessage()
            ]);
        }
    }

    private function formatPhone($phone)
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/\D/', '', $phone);

        // Remove leading 0 or 62
        if (substr($phone, 0, 1) === '0') {
            $phone = substr($phone, 1);
        } elseif (substr($phone, 0, 2) === '62') {
            $phone = substr($phone, 2);
        }

        // Add 62 prefix
        return '62' . $phone;
    }

    private function replaceTemplatePlaceholders($template, Response $response)
    {
        $answers = ResponseAnswer::with('field')->where('response_id', $response->id)->get();

        // Replace {field_name} placeholders
        foreach ($answers as $answer) {
            $fieldName = $answer->field->name ?? '';
            $template = str_replace('{' . $fieldName . '}', $answer->value, $template);
        }

        // Replace (field_name) placeholders
        foreach ($answers as $answer) {
            $fieldName = $answer->field->name ?? '';
            $template = str_replace('(' . $fieldName . ')', $answer->value, $template);
        }

        // Special handling for customer_name
        $customerName = null;
        foreach ($answers as $answer) {
            $fieldLabel = strtolower($answer->field->label ?? '');
            if (strpos($fieldLabel, 'nama') !== false ||
                strpos($fieldLabel, 'name') !== false ||
                strpos($fieldLabel, 'customer') !== false) {
                $customerName = $answer->value;
                break;
            }
        }

        if ($customerName) {
            $template = str_replace('{customer_name}', $customerName, $template);
            $template = str_replace('(customer_name)', $customerName, $template);
        }

        // Replace store placeholders
        if ($response->store) {
            $template = str_replace('{store_name}', $response->store->name, $template);
            $template = str_replace('{store_address}', $response->store->address ?? '', $template);
            $template = str_replace('{form_name}', $response->form->name, $template);
        }

        // Replace submission data placeholder
        $submissionData = '';
        foreach ($answers as $answer) {
            $fieldLabel = $answer->field->label ?? $answer->field->name ?? 'Field';
            $submissionData .= $fieldLabel . ': ' . $answer->value . "\n";
        }
        $template = str_replace('{submission_data}', $submissionData, $template);

        return $template;
    }
}