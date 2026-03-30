<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationLog;
use App\Models\Form;
use App\Exports\NotificationLogsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class NotificationLogController extends Controller
{
    /**
     * Display notification logs
     */
    public function index(Request $request)
    {
        $query = NotificationLog::with(['form', 'response', 'whatsappDevice'])->orderBy('created_at', 'desc');

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by form
        if ($request->filled('form_id')) {
            $query->where('form_id', $request->form_id);
        }

        // Search by recipient
        if ($request->filled('search')) {
            $query->where('recipient', 'like', '%' . $request->search . '%');
        }

        $logs = $query->paginate(50);
        $forms = Form::all();
        $statuses = ['pending', 'sent', 'failed'];
        $types = ['email', 'whatsapp'];

        return view('admin.notification-logs.index', compact('logs', 'forms', 'statuses', 'types'));
    }

    /**
     * Export notification logs to Excel
     */
    public function export()
    {
        return Excel::download(new NotificationLogsExport, 'notification-logs-' . now()->format('Y-m-d-H-i-s') . '.xlsx');
    }

    /**
     * Show single log detail
     */
    public function show(NotificationLog $log)
    {
        $log->load(['form', 'response']);
        return view('admin.notification-logs.show', compact('log'));
    }

    /**
     * Retry sending failed notification
     */
    public function retry(NotificationLog $log)
    {
        if ($log->type === 'whatsapp') {
            // Logic untuk retry kirim WA bisa ditambahin di sini
            // Untuk sekarang, hanya update status jadi pending
            $log->update(['status' => 'pending']);
            return back()->with('success', 'Notification queued for retry.');
        }

        return back()->with('error', 'Only WhatsApp notifications can be retried.');
    }
}
