<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsappMessage;
use App\Models\Store;
use Illuminate\Http\Request;

class WhatsappMessageController extends Controller
{
    /**
     * Display a listing of WhatsApp messages
     */
    public function index(Request $request)
    {
        $query = WhatsappMessage::query();

        if ($request->filled('device_id')) {
            $query->where('device_id', $request->device_id);
        }

        if ($request->filled('from_phone')) {
            $query->where('from_phone', 'like', '%' . $request->from_phone . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $messages = $query->orderBy('created_at', 'desc')->paginate(20);
        $stores = Store::all();
        $types = WhatsappMessage::distinct()->pluck('type');

        return view('admin.whatsapp.index', compact('messages', 'stores', 'types'));
    }

    /**
     * Show a single message
     */
    public function show(WhatsappMessage $message)
    {
        return view('admin.whatsapp.show', compact('message'));
    }

    /**
     * Delete a message
     */
    public function destroy(WhatsappMessage $message)
    {
        $message->delete();
        return redirect()->route('admin.whatsapp.index')->with('success', 'Message deleted successfully.');
    }
}
