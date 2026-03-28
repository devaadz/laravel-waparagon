<?php

// Test script untuk webhook device events
require_once __DIR__ . '/vendor/autoload.php';

use App\Models\WhatsappDevice;
use Illuminate\Http\Request;

// Simulate device registration event
$payload = [
    'event' => 'device_registered',
    'device_id' => '628123456789@s.whatsapp.net',
    'payload' => [
        'id' => '628123456789@s.whatsapp.net',
        'status' => 'disconnected',
        'phone' => '628123456789'
    ]
];

echo "Testing device registration webhook...\n";

// Create mock request
$request = new Request();
$request->merge([
    'event' => $payload['event'],
    'device_id' => $payload['device_id'],
    'payload' => $payload['payload']
]);

// Initialize Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    // Call webhook controller
    $controller = new \App\Http\Controllers\Webhook\GoWAWebhookController();
    $response = $controller->handle($request);

    echo "Webhook called successfully!\n";

    // Check if device was created
    $device = WhatsappDevice::where('device_id', $payload['device_id'])->first();
    if ($device) {
        echo "Device created in database:\n";
        echo "- ID: " . $device->device_id . "\n";
        echo "- Status: " . $device->status . "\n";
        echo "- Phone: " . ($device->phone_number ?? 'null') . "\n";
    } else {
        echo "Device not found in database!\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}