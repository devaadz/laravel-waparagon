<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GowaService;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    protected GowaService $gowaService;

    public function __construct(GowaService $gowaService)
    {
        $this->gowaService = $gowaService;
    }

    /**
     * Send WhatsApp message
     */
    public function send(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string',
            'device_id' => 'nullable|string',
        ]);

        $result = $this->gowaService->sendMessage(
            $request->phone,
            $request->message,
            $request->device_id
        );

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully',
                'data' => $result['data']
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'],
            'error' => $result['error'] ?? null
        ], 400);
    }
}
