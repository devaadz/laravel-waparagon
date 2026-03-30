<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsappMessage;
use App\Models\NotificationLog;
use App\Models\Store;
use App\Exports\NotificationLogsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class WhatsappMessageController extends Controller
{
    /**
     * Display a listing of WhatsApp notification logs (outgoing messages)
     */
    public function index(Request $request)
    {
        $query = NotificationLog::with(['form', 'response', 'whatsappDevice'])
            ->where('type', 'whatsapp')
            ->orderBy('created_at', 'desc');

        // Filter by device
        if ($request->filled('device_id')) {
            $query->where('whatsapp_device_id', $request->device_id);
        }

        // Filter by recipient phone
        if ($request->filled('recipient')) {
            $query->where('recipient', 'like', '%' . $request->recipient . '%');
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $logs = $query->paginate(50);
        $stores = Store::whereNotNull('whatsapp_device_id')->get();
        $statuses = ['pending', 'sent', 'failed'];

        return view('admin.whatsapp.index', compact('logs', 'stores', 'statuses'));
    }

    /**
     * Export WhatsApp notification logs to Excel
     */
    public function export(Request $request)
    {
        $query = NotificationLog::with(['form', 'response', 'whatsappDevice'])
            ->where('type', 'whatsapp')
            ->orderBy('created_at', 'desc');

        // Apply same filters as index
        if ($request->filled('device_id')) {
            $query->where('whatsapp_device_id', $request->device_id);
        }

        if ($request->filled('recipient')) {
            $query->where('recipient', 'like', '%' . $request->recipient . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Create a custom export class for filtered data
        $export = new class($query->get()) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithMapping {
            private $logs;

            public function __construct($logs)
            {
                $this->logs = $logs;
            }

            public function collection()
            {
                return $this->logs;
            }

            public function headings(): array
            {
                return [
                    'ID',
                    'Recipient',
                    'Device Name',
                    'Device System',
                    'Form Name',
                    'Message',
                    'Status',
                    'Error Message',
                    'Sent At',
                    'Created At',
                ];
            }

            public function map($log): array
            {
                return [
                    $log->id,
                    $log->recipient,
                    $log->device_name ?? ($log->whatsappDevice->name ?? '-'),
                    $log->device_system ?? ($log->whatsappDevice->system ?? '-'),
                    $log->form->name ?? 'N/A',
                    $log->message,
                    ucfirst($log->status),
                    $log->error_message ?? '-',
                    $log->sent_at ? $log->sent_at->format('Y-m-d H:i:s') : '-',
                    $log->created_at->format('Y-m-d H:i:s'),
                ];
            }
        };

        return Excel::download($export, 'whatsapp-logs-' . now()->format('Y-m-d-H-i-s') . '.xlsx');
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
