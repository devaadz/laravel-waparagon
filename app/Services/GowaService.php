<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\WhatsappDevice;
use App\Models\Contact;
use App\Models\WhatsappMessage;

class GowaService
{
    protected string $baseUrl;
    protected string $defaultDeviceId;
    protected ?string $username;
    protected ?string $password;

    public function __construct()
    {
        $this->baseUrl = config('services.gowa.url', env('GOWA_URL'));
        $this->defaultDeviceId = config('services.gowa.device_id', env('GOWA_DEVICE_ID', 'default_device'));
        $this->username = env('GOWA_USERNAME');
        $this->password = env('GOWA_PASSWORD');
    }

    /**
     * Get HTTP client with authentication if configured
     */
    protected function getHttpClient()
    {
        $http = Http::timeout(30);
        
        if ($this->username && $this->password) {
            $http = $http->withBasicAuth($this->username, $this->password);
        }
        
        return $http;
    }

    /**
     * Create a new device in GoWA
     */
    public function createDevice(string $deviceId, string $name = null): array
    {
        try {
            $response = $this->getHttpClient()->post("{$this->baseUrl}/devices", [
                'device_id' => $deviceId,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Save to Laravel database
                WhatsappDevice::create([
                    'name' => $name,
                    'device_id' => $deviceId,
                    'status' => 'disconnected',
                ]);

                Log::info('Device created successfully', [
                    'device_id' => $deviceId,
                    'gowa_response' => $data
                ]);

                return [
                    'success' => true,
                    'data' => $data,
                    'message' => 'Device created successfully'
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to create device in GoWA',
                'error' => $response->body()
            ];

        } catch (\Exception $e) {
            Log::error('Error creating device', [
                'device_id' => $deviceId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Error creating device',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get QR code for device login
     */
    public function getDeviceLoginQr(string $deviceId): array
    {
        try {
            $response = $this->getHttpClient()->post("{$this->baseUrl}/api/login", [
                'device_id' => $deviceId,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                Log::info('Device login QR retrieved', ['device_id' => $deviceId]);

                return [
                    'success' => true,
                    'data' => $data,
                    'message' => 'QR code retrieved successfully'
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to get QR code',
                'error' => $response->body()
            ];

        } catch (\Exception $e) {
            Log::error('Error getting device login QR', [
                'device_id' => $deviceId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Error getting QR code',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get all devices from GoWA and sync with Laravel
     */
    public function getDevices(): array
    {
        try {
            $response = $this->getHttpClient()->get("{$this->baseUrl}/devices");

            if ($response->successful()) {
                $data = $response->json();
                $devices = $data['data'] ?? $data['results'] ?? [];

                // Sync with Laravel database
                foreach ($devices as $deviceData) {
                    $deviceId = $deviceData['id'] ?? $deviceData['device'] ?? null;
                    if (!$deviceId) {
                        continue; // Skip invalid device data
                    }

                    $device = WhatsappDevice::updateOrCreate(
                        ['device_id' => $deviceId],
                        [
                            'name' => $deviceData['name'] ?? null,
                            'status' => $deviceData['state'] ?? $this->mapDeviceStatus($deviceData),
                            'phone_number' => $deviceData['phone'] ?? $deviceData['phone_number'] ?? null,
                            'is_logged_in' => $this->isDeviceLoggedIn($deviceData),
                        ]
                    );

                    // Update store phone number if device is linked and logged in
                    if ($device->is_logged_in && $device->phone_number) {
                        $store = \App\Models\Store::where('whatsapp_device_id', $deviceId)->first();
                        if ($store && (!$store->phone_number || $store->phone_number !== $device->phone_number)) {
                            $store->update(['phone_number' => $device->phone_number]);
                            Log::info('Store phone number updated from device', [
                                'store_id' => $store->id,
                                'device_id' => $deviceId,
                                'phone_number' => $device->phone_number
                            ]);
                        }
                    }
                }

                Log::info('Devices synced successfully', ['count' => count($devices)]);

                return [
                    'success' => true,
                    'data' => $devices,
                    'message' => 'Devices synced successfully'
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to get devices from GoWA',
                'error' => $response->body()
            ];

        } catch (\Exception $e) {
            Log::error('Error getting devices', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => 'Error getting devices',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send WhatsApp message
     */
    public function sendMessage(string $phone, string $message, ?string $deviceId = null): array
    {
        $deviceId = $deviceId ?? $this->defaultDeviceId;

        try {
            $response = $this->getHttpClient()->withHeaders([
                'X-Device-Id' => $deviceId,
            ])->post("{$this->baseUrl}/send/message", [
                'phone' => $phone,
                'message' => $message,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Save message to database
                WhatsappMessage::create([
                    'device_id' => $deviceId,
                    'to_phone' => $phone,
                    'text' => $message,
                    'type' => 'text',
                    'is_from_me' => true,
                ]);

                // Update or create contact
                Contact::updateOrCreate(
                    ['phone_number' => $phone],
                    ['name' => null] // Could be updated later from webhook
                );

                Log::info('Message sent successfully', [
                    'device_id' => $deviceId,
                    'phone' => $phone
                ]);

                return [
                    'success' => true,
                    'data' => $data,
                    'message' => 'Message sent successfully'
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to send message',
                'error' => $response->body()
            ];

        } catch (\Exception $e) {
            Log::error('Error sending message', [
                'device_id' => $deviceId,
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Error sending message',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send WhatsApp image
     */
    public function sendImage(string $phone, string $imagePath, string $caption = '', ?string $deviceId = null): array
    {
        $deviceId = $deviceId ?? $this->defaultDeviceId;

        try {
            $response = $this->getHttpClient()->withHeaders([
                'X-Device-Id' => $deviceId,
            ])->attach('image', file_get_contents($imagePath), basename($imagePath))
            ->post("{$this->baseUrl}/send/image", [
                'phone' => $phone,
                'caption' => $caption
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Save message to database
                WhatsappMessage::create([
                    'device_id' => $deviceId,
                    'to_phone' => $phone,
                    'text' => $caption ?: 'Image sent',
                    'type' => 'image',
                    'media_url' => $imagePath,
                    'is_from_me' => true,
                ]);

                // Update or create contact
                Contact::updateOrCreate(
                    ['phone_number' => $phone],
                    ['name' => null]
                );

                Log::info('Image sent successfully', [
                    'device_id' => $deviceId,
                    'phone' => $phone
                ]);

                return [
                    'success' => true,
                    'data' => $data,
                    'message' => 'Image sent successfully'
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to send image',
                'error' => $response->body()
            ];

        } catch (\Exception $e) {
            Log::error('Error sending image', [
                'device_id' => $deviceId,
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Error sending image',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Map GoWA device status to Laravel status
     */
    private function mapDeviceStatus(array $deviceData): string
    {
        $state = $deviceData['state'] ?? $deviceData['status'] ?? 'disconnected';

        // Map GoWA states to Laravel statuses
        switch (strtolower($state)) {
            case 'connected':
            case 'ready':
            case 'authenticated':
                return 'connected';
            case 'connecting':
            case 'connecting...':
                return 'connecting';
            case 'disconnected':
            case 'logged_out':
            default:
                return 'disconnected';
        }
    }

    /**
     * Check if device is logged in based on device data
     */
    private function isDeviceLoggedIn(array $deviceData): bool
    {
        $state = $deviceData['state'] ?? $deviceData['status'] ?? 'disconnected';
        $phone = $deviceData['phone'] ?? $deviceData['phone_number'] ?? null;

        // Device is considered logged in if it has a phone number and is in connected state
        return in_array(strtolower($state), ['connected', 'ready', 'authenticated']) && !empty($phone);
    }
}