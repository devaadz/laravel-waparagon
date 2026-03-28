# WA Paragon - GoWA Device & WhatsApp Management Documentation

## Overview
WA Paragon adalah aplikasi Laravel yang terintegrasi dengan GoWA (Go WhatsApp Web Multi-Device) untuk mengelola perangkat WhatsApp dan mengirim pesan secara otomatis. Sistem ini mendukung multi-device WhatsApp dengan tracking lengkap di database MySQL.

## Arsitektur Sistem

### Komponen Utama
1. **Laravel Backend** - Mengelola database, admin panel, dan API
2. **GoWA API** - Service WhatsApp multi-device
3. **Webhook Integration** - Sinkronisasi real-time antara GoWA dan Laravel
4. **MySQL Database** - Penyimpanan data device, pesan, dan form responses

### Database Schema
```sql
-- Devices table
CREATE TABLE whatsapp_devices (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    device_id VARCHAR(255) UNIQUE NOT NULL,
    phone_number VARCHAR(255),
    status ENUM('connected', 'disconnected', 'connecting') DEFAULT 'disconnected',
    is_logged_in BOOLEAN DEFAULT FALSE,
    device_info JSON,
    store_id BIGINT UNSIGNED NULL,
    last_login_at TIMESTAMP NULL,
    last_logout_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (store_id) REFERENCES stores(id)
);

-- Messages table
CREATE TABLE whatsapp_messages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    device_id VARCHAR(255) NOT NULL,
    message_id VARCHAR(255) UNIQUE NOT NULL,
    from_phone VARCHAR(255),
    to_phone VARCHAR(255),
    text TEXT,
    type VARCHAR(50) DEFAULT 'text',
    media_url VARCHAR(500),
    payload JSON,
    is_from_me BOOLEAN DEFAULT FALSE,
    message_timestamp TIMESTAMP,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (device_id) REFERENCES whatsapp_devices(device_id)
);
```

## Setup & Konfigurasi

### 1. Environment Setup
```bash
# Clone repositories
git clone <laravel-repo> waparagon
git clone <gowa-repo> gowa-docker

# Install Laravel dependencies
cd waparagon
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=waparagon
DB_USERNAME=root
DB_PASSWORD=

# Run migrations
php artisan migrate
```

### 2. GoWA Setup
```bash
cd gowa-docker/go-whatsapp-web-multidevice

# Configure environment
cp src/.env.example src/.env
# Edit src/.env with webhook URL
WEBHOOK_URL=http://localhost:8000/webhook/gowa
WEBHOOK_EVENTS=device_registered,device_connected,device_disconnected,device_login,device_logout,message

# Build and run
docker-compose up -d
```

### 3. Webhook Configuration
Webhook endpoint di Laravel: `POST /webhook/gowa`

Payload format:
```json
{
  "event": "device_registered|device_connected|device_disconnected|device_login|device_logout|message",
  "device_id": "628123456789@s.whatsapp.net",
  "payload": {
    // Event-specific data
  }
}
```

## Device Management

### 1. Device Registration Flow
1. User membuat device baru di GoWA
2. GoWA mengirim webhook `device_registered`
3. Laravel menyimpan device ke database
4. Admin dapat melihat device di panel admin
5. Admin dapat link device ke store

### 2. Device Status Tracking
- **disconnected**: Device offline
- **connected**: Device online tapi belum login
- **logged_in**: Device aktif dan siap digunakan

### 3. Admin Panel Features
- **Device List**: Lihat semua device dengan status
- **Device Detail**: Info lengkap device dan history
- **Store Linking**: Hubungkan device ke store tertentu
- **Sync**: Sinkronisasi manual dengan GoWA API
- **Delete**: Hapus device yang tidak digunakan

## WhatsApp Messaging

### 1. Send Message API
```php
// Via Laravel Controller
use Illuminate\Support\Facades\Http;

$response = Http::post('http://localhost:3000/api/send-message', [
    'device_id' => '628123456789@s.whatsapp.net',
    'phone' => '628987654321',
    'message' => 'Hello from WA Paragon!'
]);
```

### 2. Message Types Supported
- **Text**: Pesan teks biasa
- **Image**: Pesan dengan gambar
- **Video**: Pesan dengan video
- **Audio**: Pesan suara
- **Document**: File dokumen
- **Contact**: Kontak WhatsApp

### 3. Message Storage
Semua pesan otomatis disimpan ke database dengan:
- Message ID (unik dari WhatsApp)
- Device ID (pengirim)
- Phone numbers (dari/ke)
- Content dan metadata
- Timestamp asli dari WhatsApp

## API Reference

### GoWA API Endpoints

#### Send Message
```http
POST http://localhost:3000/api/send-message
Content-Type: application/json

{
  "device_id": "628123456789@s.whatsapp.net",
  "phone": "628987654321",
  "message": "Your message here"
}
```

#### Get Device Info
```http
GET http://localhost:3000/api/device/628123456789@s.whatsapp.net
```

#### Login Device (QR Code)
```http
POST http://localhost:3000/api/login
Content-Type: application/json

{
  "device_id": "628123456789@s.whatsapp.net"
}
```

#### Login Device (Code)
```http
POST http://localhost:3000/api/login-code
Content-Type: application/json

{
  "device_id": "628123456789@s.whatsapp.net",
  "phone": "628123456789"
}
```

### Laravel API Endpoints

#### Webhook
```http
POST /webhook/gowa
Content-Type: application/json

{
  "event": "device_registered",
  "device_id": "628123456789@s.whatsapp.net",
  "payload": {...}
}
```

#### Admin Device Management
```http
GET /admin/devices              # List devices
GET /admin/devices/{id}         # Show device detail
POST /admin/devices/sync        # Sync with GoWA
POST /admin/devices/{id}/link-store   # Link to store
POST /admin/devices/{id}/unlink-store # Unlink from store
DELETE /admin/devices/{id}      # Delete device
```

## Testing & Troubleshooting

### 1. Test Device Registration
```bash
# Test webhook dengan curl
curl -X POST http://localhost:8000/webhook/gowa \
  -H "Content-Type: application/json" \
  -d '{
    "event": "device_registered",
    "device_id": "628123456789@s.whatsapp.net",
    "payload": {
      "id": "628123456789@s.whatsapp.net",
      "status": "disconnected",
      "phone": "628123456789"
    }
  }'
```

### 2. Test Message Sending
```bash
# Via GoWA API
curl -X POST http://localhost:3000/api/send-message \
  -H "Content-Type: application/json" \
  -d '{
    "device_id": "628123456789@s.whatsapp.net",
    "phone": "628987654321",
    "message": "Test message from WA Paragon"
  }'
```

### 3. Common Issues

#### Device tidak masuk database
- Pastikan webhook URL benar di GoWA config
- Cek Laravel logs untuk error webhook
- Verifikasi database connection

#### Message gagal terkirim
- Pastikan device status 'logged_in'
- Cek device_id format (harus @s.whatsapp.net)
- Verifikasi GoWA service running

#### Webhook tidak diterima
- Cek firewall dan port accessibility
- Pastikan Laravel server running
- Verify webhook endpoint route exists

### 4. Monitoring & Logs
```bash
# Laravel logs
tail -f storage/logs/laravel.log

# GoWA logs
docker-compose logs -f gowa
```

## Production Deployment

### 1. Security Considerations
- Gunakan HTTPS untuk webhook
- Implementasi authentication untuk API calls
- Rate limiting untuk webhook endpoints
- Database credentials encryption

### 2. Performance Optimization
- Database indexing pada device_id dan message_id
- Queue system untuk bulk messaging
- Caching untuk device status
- Load balancing untuk high traffic

### 3. Backup & Recovery
- Regular database backup
- Device credentials backup
- Log rotation setup
- Disaster recovery plan

## Development Notes

### Code Structure
```
app/
├── Http/Controllers/
│   ├── Admin/
│   │   ├── WhatsappDeviceController.php
│   │   └── WhatsappMessageController.php
│   └── Webhook/
│       └── GoWAWebhookController.php
├── Models/
│   ├── WhatsappDevice.php
│   └── WhatsappMessage.php
└── ...
```

### Key Classes
- **GoWAWebhookController**: Handle webhook events
- **WhatsappDeviceController**: Admin device management
- **WhatsappDevice**: Device model dengan relationships
- **WhatsappMessage**: Message model dengan media handling

### Events Handled
- `device_registered`: Device baru didaftarkan
- `device_connected`: Device terhubung
- `device_disconnected`: Device terputus
- `device_login`: User login ke device
- `device_logout`: User logout dari device
- `message`: Pesan baru diterima/dikirim

---

**Version**: 1.0
**Last Updated**: March 7, 2026
**Author**: WA Paragon Team