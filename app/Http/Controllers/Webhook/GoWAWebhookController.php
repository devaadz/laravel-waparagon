<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\WhatsappMessage;
use App\Models\WhatsappDevice;
use Illuminate\Http\Request;

class GoWAWebhookController extends Controller
{
    /**
     * Handle incoming webhook from GoWA
     */
    public function handle(Request $request)
    {
        try {
            $event = $request->input('event');
            $deviceId = $request->input('device_id');
            $payload = $request->input('payload', []);

            if ($event === 'message') {
                $this->handleMessageEvent($deviceId, $payload);
            } elseif (in_array($event, ['device_connected', 'device_disconnected', 'device_login', 'device_logout', 'device_registered'])) {
                $this->handleDeviceEvent($event, $deviceId, $payload);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('GoWA Webhook Error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle message event from GoWA
     */
    private function handleMessageEvent($deviceId, $payload)
    {
        $message = $payload['message'] ?? [];
        $messageId = $message['id'] ?? null;

        if (!$messageId) {
            return;
        }

        // Check if message already exists
        $existing = WhatsappMessage::where('message_id', $messageId)->first();
        if ($existing) {
            return;
        }

        // Parse message data
        $fromPhone = $message['from_me'] ? ($message['participant'] ?? null) : $message['key']['remoteJid'] ?? null;
        $toPhone = $message['from_me'] ? $message['key']['remoteJid'] : null;
        $text = $message['body'] ?? null;
        $type = $this->getMessageType($message);
        $timestamp = isset($message['messageTimestamp']) ? now()->timestamp($message['messageTimestamp']) : now();

        // Store message in database
        WhatsappMessage::create([
            'device_id' => $deviceId,
            'message_id' => $messageId,
            'from_phone' => $fromPhone,
            'to_phone' => $toPhone,
            'text' => $text,
            'type' => $type,
            'media_url' => $this->getMediaUrl($message),
            'payload' => $message,
            'is_from_me' => $message['from_me'] ?? false,
            'message_timestamp' => $timestamp,
        ]);

        \Log::info('Stored WhatsApp message', [
            'message_id' => $messageId,
            'device_id' => $deviceId,
        ]);
    }

    /**
     * Handle device event from GoWA
     */
    private function handleDeviceEvent($event, $deviceId, $payload)
    {
        // Find or create device record
        $device = WhatsappDevice::firstOrNew(['device_id' => $deviceId]);

        switch ($event) {
            case 'device_registered':
                $device->device_info = $payload;
                $device->status = 'disconnected';
                $device->is_logged_in = false;
                $device->phone_number = $payload['phone'] ?? $device->phone_number;
                break;

            case 'device_connected':
                $device->status = 'connected';
                $device->device_info = array_merge($device->device_info ?? [], $payload);
                break;

            case 'device_disconnected':
                $device->status = 'disconnected';
                break;

            case 'device_login':
                $device->is_logged_in = true;
                $device->last_login_at = now();
                $device->phone_number = $payload['phone'] ?? $device->phone_number;
                $device->status = 'connected';
                $device->device_info = array_merge($device->device_info ?? [], $payload);
                break;

            case 'device_logout':
                $device->is_logged_in = false;
                $device->last_logout_at = now();
                $device->status = 'disconnected';
                break;
        }

        $device->save();

        \Log::info('Updated WhatsApp device', [
            'event' => $event,
            'device_id' => $deviceId,
            'status' => $device->status,
            'is_logged_in' => $device->is_logged_in,
        ]);
    }

    /**
     * Determine message type
     */
    private function getMessageType($message): string
    {
        if (isset($message['body'])) {
            return 'text';
        } elseif (isset($message['imageMessage'])) {
            return 'image';
        } elseif (isset($message['videoMessage'])) {
            return 'video';
        } elseif (isset($message['audioMessage'])) {
            return 'audio';
        } elseif (isset($message['documentMessage'])) {
            return 'document';
        } elseif (isset($message['contactMessage'])) {
            return 'contact';
        }

        return 'unknown';
    }

    /**
     * Extract media URL from message
     */
    private function getMediaUrl($message): ?string
    {
        $mediaKeys = ['imageMessage', 'videoMessage', 'audioMessage', 'documentMessage'];

        foreach ($mediaKeys as $key) {
            if (isset($message[$key]['url'])) {
                return $message[$key]['url'];
            }
        }

        return null;
    }
}
