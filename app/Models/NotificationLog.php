<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    protected $table = 'notification_logs';
    
    protected $fillable = [
        'form_id',
        'response_id',
        'type',
        'recipient',
        'message',
        'status',
        'error_message',
        'sent_at',
        'whatsapp_device_id',
        'device_name',
        'device_system',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function response()
    {
        return $this->belongsTo(Response::class);
    }

    public function whatsappDevice()
    {
        return $this->belongsTo(WhatsappDevice::class, 'whatsapp_device_id');
    }

    public static function logNotification($formId, $responseId, $type, $recipient, $message, $status = 'pending', $errorMessage = null, $whatsappDeviceId = null, $deviceName = null, $deviceSystem = null)
    {
        return self::create([
            'form_id' => $formId,
            'response_id' => $responseId,
            'type' => $type,
            'recipient' => $recipient,
            'message' => $message,
            'status' => $status,
            'error_message' => $errorMessage,
            'sent_at' => $status === 'sent' ? now() : null,
            'whatsapp_device_id' => $whatsappDeviceId,
            'device_name' => $deviceName,
            'device_system' => $deviceSystem,
        ]);
    }

    public static function markAsSent($id)
    {
        $log = self::find($id);
        if ($log) {
            $log->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        }
        return $log;
    }

    public static function markAsFailed($id, $errorMessage)
    {
        $log = self::find($id);
        if ($log) {
            $log->update([
                'status' => 'failed',
                'error_message' => $errorMessage,
            ]);
        }
        return $log;
    }
}
