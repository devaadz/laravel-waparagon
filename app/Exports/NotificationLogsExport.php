<?php

namespace App\Exports;

use App\Models\NotificationLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class NotificationLogsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return NotificationLog::with(['form', 'response', 'whatsappDevice'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Define the headings for the Excel file
     */
    public function headings(): array
    {
        return [
            'ID',
            'Type',
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

    /**
     * Map the data for each row
     */
    public function map($log): array
    {
        return [
            $log->id,
            ucfirst($log->type),
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
}
