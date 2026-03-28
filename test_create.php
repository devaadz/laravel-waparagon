<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = app(\App\Services\GowaService::class);

// Test create device
$result = $service->createDevice('test_device_laravel', 'Test Device Laravel');
echo "Create result:\n";
print_r($result);

// Check devices in DB
echo "\nDevices in DB:\n";
$devices = \App\Models\WhatsappDevice::all();
foreach ($devices as $device) {
    echo "- {$device->name}: {$device->device_id} ({$device->status})\n";
}