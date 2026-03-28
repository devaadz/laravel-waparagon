<?php

// Simple test to check GoWA API
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'http://localhost:3000/devices');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "GoWA API Status: $httpCode\n";
if ($httpCode == 200) {
    echo "Response: " . substr($response, 0, 200) . "...\n";
} else {
    echo "Error: $response\n";
}

// Test device login QR
echo "\nTesting device login QR...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:3000/devices/device_001/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Login QR Status: $httpCode\n";
if ($httpCode == 200) {
    echo "QR Response: " . substr($response, 0, 200) . "...\n";
} else {
    echo "Error: $response\n";
}