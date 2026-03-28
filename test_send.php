<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = app(\App\Services\GowaService::class);

// Test send message
$result = $service->sendMessage('628123456789', 'Test message from Laravel', 'test_device_laravel');
echo "Send result:\n";
print_r($result);

// Check messages in DB
echo "\nMessages in DB:\n";
$messages = \App\Models\WhatsappMessage::latest()->take(5)->get();
foreach ($messages as $msg) {
    echo "- To: {$msg->to_phone}, Message: {$msg->text}, Device: {$msg->device_id}\n";
}