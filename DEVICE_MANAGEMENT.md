# GoWA Device Management Integration

## Overview
WA Paragon sekarang terintegrasi penuh dengan GoWA untuk manajemen device WhatsApp. Setiap device yang didaftarkan di GoWA akan otomatis tercatat di database Laravel.

## Setup Instructions

### 1. Configure GoWA Webhook untuk Device Events

GoWA perlu mengirim webhook events untuk device management:

```yaml
# Di docker-compose.yml atau config GoWA
WEBHOOK_URL=http://your-laravel-domain.com/webhook/gowa
WEBHOOK_EVENTS=device_registered,device_connected,device_disconnected,device_login,device_logout,message
```

**Atau via GoWA API:**
```bash
curl -X POST http://localhost:3000/app/webhook \
  -H "Content-Type: application/json" \
  -d '{
    "url": "http://your-laravel-domain/webhook/gowa",
    "events": ["device_registered", "device_connected", "device_disconnected", "device_login", "device_logout", "message"]
  }'
```

### 2. Device Events yang Didukung

Laravel akan menerima dan memproses event berikut:

| Event | Description | Action |
|-------|-------------|--------|
| `device_registered` | Device baru didaftarkan | Create device record |
| `device_connected` | Device terhubung ke WhatsApp | Update status ke 'connected' |
| `device_disconnected` | Device terputus | Update status ke 'disconnected' |
| `device_login` | User login ke WhatsApp | Update login status & phone |
| `device_logout` | User logout dari WhatsApp | Update logout status |

### 3. Admin Dashboard

**Admin > Devices** - Kelola semua device WhatsApp:

- ✅ View semua device yang terdaftar
- ✅ Filter by status (connected/disconnected) dan login status
- ✅ Link device ke store untuk multi-account messaging
- ✅ View device details dan recent messages
- ✅ Sync devices dari GoWA API
- ✅ Delete device (jika tidak linked ke store)

## Database Schema

### whatsapp_devices Table
```sql
- id: Primary key
- device_id: Unique GoWA device identifier
- phone_number: WhatsApp phone number
- status: connected/disconnected/connecting
- is_logged_in: Boolean login status
- last_login_at: Timestamp last login
- last_logout_at: Timestamp last logout
- device_info: Full device info JSON
- created_at, updated_at: Timestamps
```

## Workflow

### 1. Device Registration
```
GoWA → Register Device → Webhook "device_registered" → Laravel creates device record
```

### 2. Device Login
```
GoWA → User scans QR/login → Webhook "device_login" → Laravel updates device status
```

### 3. Message Sending
```
Form Submit → Laravel checks store.device_id → Send via GoWA with Device-Id header
```

### 4. Message Receiving
```
Incoming Message → Webhook "message" → Laravel stores in whatsapp_messages table
```

## API Endpoints

### Webhook
- `POST /webhook/gowa` - Receives all GoWA events

### Admin Routes
- `GET /admin/devices` - List devices
- `GET /admin/devices/{id}` - Show device details
- `POST /admin/devices/sync` - Sync from GoWA API
- `POST /admin/devices/{id}/link-store` - Link device to store
- `POST /admin/devices/{id}/unlink-store` - Unlink device from store
- `DELETE /admin/devices/{id}` - Delete device

## Testing

### 1. Manual Device Registration
```bash
# Register device via GoWA API
curl -X POST http://localhost:3000/devices \
  -H "Content-Type: application/json" \
  -d '{}'
```

### 2. Check Webhook
```bash
# Test webhook dengan device event
curl -X POST http://localhost:8000/webhook/gowa \
  -H "Content-Type: application/json" \
  -d '{
    "event": "device_registered",
    "device_id": "628123456789@s.whatsapp.net",
    "payload": {
      "id": "628123456789@s.whatsapp.net",
      "status": "disconnected"
    }
  }'
```

### 3. Sync Devices
- Go to **Admin > Devices**
- Click **"Sync from GoWA"** button
- Devices akan di-sync dari GoWA API

### 4. Link to Store
- Edit store di **Admin > Stores**
- Masukkan Device ID yang sudah registered
- Atau dari device detail page, link ke store

## Troubleshooting

### Device tidak muncul di Laravel
1. Pastikan GoWA webhook configured dengan benar
2. Check Laravel logs: `storage/logs/laravel.log`
3. Test webhook manually seperti di atas

### Sync gagal
1. Pastikan GoWA API accessible dari Laravel
2. Check network connection antara containers
3. Verify GoWA API endpoint `/app/devices` available

### Device ID format
- Format harus: `628xxxxxxxxx@s.whatsapp.net`
- Include `@s.whatsapp.net` suffix
- Case sensitive

## Next Steps

Potential enhancements:
- Auto-sync devices periodically
- Device health monitoring
- Bulk device operations
- Device usage analytics
- Integration dengan WhatsApp Business API
- Two-way messaging interface

---

**Result**: Setiap device yang didaftarkan di GoWA sekarang otomatis tercatat di Laravel database! 🎉