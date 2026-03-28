<?php

require_once 'vendor/autoload.php';

use App\Services\GowaService;
use Illuminate\Support\Facades\DB;

$gowaService = new GowaService();

// Get first device from database
$devices = DB::table('whatsapp_devices')->get();

if ($devices->isEmpty()) {
    echo "No devices found. Please create a device first.\n";
    exit;
}

$device = $devices->first();
echo "Testing login QR for device: {$device->name} (ID: {$device->device_id})\n\n";

$result = $gowaService->getDeviceLoginQr($device->device_id);

echo "Login QR result:\n";
print_r($result);

echo "\nQR Data:\n";
if (isset($result['data']['qr'])) {
    echo "QR Code available (base64 image)\n";
} else {
    echo "No QR code in response\n";
}