<?php

try {
    $pdo = new PDO('mysql:host=localhost;dbname=waparagon', 'root', '');
    $stmt = $pdo->query('SELECT * FROM whatsapp_devices ORDER BY created_at DESC LIMIT 5');
    $devices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo 'Devices in database:' . PHP_EOL;
    foreach ($devices as $device) {
        echo '- ID: ' . $device['device_id'] . ', Status: ' . $device['status'] . ', Phone: ' . ($device['phone_number'] ?? 'null') . ', Created: ' . $device['created_at'] . PHP_EOL;
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}