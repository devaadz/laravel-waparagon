# GoWA + Laravel Integration Guide

## Overview
WA Paragon now integrates with GoWA (Go WhatsApp Web Multidevice) to:
- Send WhatsApp messages from multiple store accounts
- Store all incoming WhatsApp messages in Laravel database
- View and manage WhatsApp conversations in the admin panel

## Setup Instructions

### 1. Configure Store WhatsApp Devices

1. Go to **Admin > Stores**
2. Create or edit a store
3. Add the **WhatsApp Device ID** (format: `628123456789@s.whatsapp.net`)
   - Get this from your GoWA instance after logging in

### 2. Configure GoWA Webhook

GoWA needs to be configured to send webhook events to Laravel.

**Webhook URL**: 
```
http://your-laravel-domain.com/webhook/gowa
```

**Example GoWA Configuration** (if using docker-compose):
```yaml
# In your GoWA docker-compose.yml or config
WEBHOOK_URL=http://laravel-app:8000/webhook/gowa
WEBHOOK_EVENTS=message  # or all events you want to track
```

**Or configure via GoWA API** (if available):
```bash
curl -X POST http://localhost:3000/app/webhook \
  -H "Content-Type: application/json" \
  -d '{
    "url": "http://your-laravel-domain.com/webhook/gowa",
    "events": ["message"]
  }'
```

### 3. Webhook Payload Structure

GoWA sends webhook events in this format:

```json
{
  "event": "message",
  "device_id": "628123456789@s.whatsapp.net",
  "payload": {
    "message": {
      "id": "3EB0...",
      "key": {
        "remoteJid": "628123456789@s.whatsapp.net",
        "fromMe": false,
        "id": "3EB0..."
      },
      "body": "Hello!",
      "messageTimestamp": 1646671203,
      "from_me": false,
      ...
    }
  }
}
```

## Features Implemented

### ✅ Multiple WhatsApp Devices
- Each store can have its own WhatsApp device ID
- Messages are sent from the store's associated WhatsApp account
- Device ID is passed via `Device-Id` header in requests

### ✅ Message Storage
- All incoming messages are stored in `whatsapp_messages` table
- Stores message ID, sender, recipient, type, content
- Full payload is stored as JSON for reference

### ✅ Admin Dashboard
- View all incoming WhatsApp messages: **Admin > WhatsApp**
- Filter by device, phone number, or message type
- View full message details and payload
- Supports pagination

### ✅ Message Types Supported
- text
- image
- video
- audio
- document
- contact
- unknown (for unsupported types)

## Database Schema

### whatsapp_messages Table
```sql
- id: Primary key
- device_id: Store's WhatsApp device identifier
- message_id: Unique GoWA message ID
- from_phone: Sender phone number
- to_phone: Recipient phone number (for outgoing)
- text: Message text (if applicable)
- type: Message type (text, image, video, etc)
- media_url: URL to media (if applicable)
- payload: Full message payload as JSON
- is_from_me: Boolean (true if outgoing)
- message_timestamp: When message was sent
- created_at, updated_at: When stored in Laravel
```

## Sending Messages

When a form is submitted:
1. Laravel sends message via `/send/message` endpoint
2. Includes `Device-Id` header matching store's device ID
3. GoWA sends message from that device
4. Webhook returns and message is stored in database

## Testing

### 1. Check Webhook Setup
```bash
# From Laravel project
php artisan tinker
```

```php
// Check if messages are being stored
App\Models\WhatsappMessage::latest()->first();
```

### 2. Manual Test
```bash
# Send test event to webhook
curl -X POST http://localhost:8000/webhook/gowa \
  -H "Content-Type: application/json" \
  -d '{
    "event": "message",
    "device_id": "628123456789@s.whatsapp.net",
    "payload": {
      "message": {
        "id": "test123",
        "body": "Test message",
        "from_me": false,
        "messageTimestamp": '$(date +%s)',
        "key": {
          "remoteJid": "628987654321@s.whatsapp.net",
          "fromMe": false,
          "id": "test123"
        }
      }
    }
  }'
```

### 3. View in Admin
- Go to **Admin > WhatsApp**
- Filter by device ID
- You should see the test message

## Troubleshooting

### Webhook not receiving messages
1. Check GoWA webhook configuration
2. Verify Firebase/network between GoWA and Laravel
3. Check Laravel logs: `storage/logs/laravel.log`
4. Ensure WEBHOOK_URL is reachable from GoWA container

### Messages not storing
1. Check if migration ran: `php artisan migrate:status`
2. Check Laravel logs for webhook errors
3. Verify database connection

### Device ID not found
1. Ensure WhatsApp is logged in on GoWA
2. Get correct device ID from GoWA `/app/devices` endpoint
3. Format must include `@s.whatsapp.net` suffix

## Next Steps

Potential enhancements:
- Reply to messages from admin panel
- Group message handling
- Message templates
- Automatic responses
- Message forwarding to store owner
- Two-way chat integration

