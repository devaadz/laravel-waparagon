<?php

// Simple test for webhook
echo "Testing webhook device registration...\n";

// Check if we can connect to database
try {
    $pdo = new PDO('mysql:host=localhost;dbname=waparagon', 'root', '');
    echo "Database connected successfully\n";

    // Simulate device data
    $deviceId = '628123456789@s.whatsapp.net';
    $phone = '628123456789';
    $status = 'disconnected';

    // Check if device exists
    $stmt = $pdo->prepare("SELECT * FROM whatsapp_devices WHERE device_id = ?");
    $stmt->execute([$deviceId]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        echo "Device already exists: " . $existing['device_id'] . "\n";
    } else {
        // Insert device
        $result = $stmt->execute([$deviceId, $phone, $status, 0, json_encode(['id' => $deviceId, 'status' => $status, 'phone' => $phone])]);

        if ($result) {
            echo "Device inserted successfully\n";

            // Verify
            $stmt = $pdo->prepare("SELECT * FROM whatsapp_devices WHERE device_id = ?");
            $stmt->execute([$deviceId]);
            $device = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "Device in DB: " . $device['device_id'] . ", Status: " . $device['status'] . ", Phone: " . $device['phone_number'] . "\n";
        } else {
            echo "Failed to insert device\n";
        }
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}